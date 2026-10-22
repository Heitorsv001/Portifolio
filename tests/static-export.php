<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli' || !class_exists(DOMDocument::class)) {
    fwrite(STDERR, "Os testes de exportação exigem PHP CLI 8.2+ com ext-dom.\n");
    exit(1);
}

$root = dirname(__DIR__);
$workspace = sys_get_temp_dir() . '/portfolio-static-tests-' . bin2hex(random_bytes(8));
$checks = 0;
$failures = [];
$expect = static function (bool $condition, string $message) use (&$checks, &$failures): void {
    $checks++;
    if (!$condition) {
        $failures[] = $message;
    }
};
$copyTree = static function (string $source, string $destination) use (&$copyTree): void {
    mkdir($destination, 0777, true);
    foreach (new DirectoryIterator($source) as $entry) {
        if ($entry->isDot()) {
            continue;
        }
        if ($entry->isLink()) {
            throw new RuntimeException('Não copiar links simbólicos para as fixtures.');
        }
        $target = $destination . '/' . $entry->getFilename();
        if ($entry->isDir()) {
            $copyTree($entry->getPathname(), $target);
        } else {
            copy($entry->getPathname(), $target);
        }
    }
};
$snapshot = static function (string $directory): array {
    $files = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS));
    foreach ($iterator as $entry) {
        $files[substr($entry->getPathname(), strlen($directory) + 1)] = hash_file('sha256', $entry->getPathname());
    }
    ksort($files);
    return $files;
};
$environment = getenv();
unset($environment['APP_STATIC_EXPORT']);
$environment['APP_BASE_PATH'] = '';
$environment['APP_ORIGIN'] = 'https://portfolio.example';
$run = static function (string $script, array $arguments = [], ?array $env = null) use ($workspace, &$environment): array {
    $stdout = tmpfile();
    $stderr = tmpfile();
    $selected = $env ?? $environment;
    $previous = [];
    foreach (['APP_BASE_PATH', 'APP_ORIGIN', 'APP_STATIC_EXPORT'] as $name) {
        $previous[$name] = getenv($name);
        putenv(array_key_exists($name, $selected) ? $name . '=' . $selected[$name] : $name);
    }
    // Herdar o ambiente preserva APP_BASE_PATH vazio, omitido por algumas versões de proc_open com env explícito.
    $process = proc_open([PHP_BINARY, $workspace . '/' . $script, ...$arguments], [0 => ['pipe', 'r'], 1 => $stdout, 2 => $stderr], $pipes, $workspace);
    foreach ($previous as $name => $value) {
        putenv($value === false ? $name : $name . '=' . $value);
    }
    fclose($pipes[0]);
    $status = proc_close($process);
    rewind($stdout);
    rewind($stderr);
    $result = ['status' => $status, 'stdout' => stream_get_contents($stdout), 'stderr' => stream_get_contents($stderr)];
    fclose($stdout);
    fclose($stderr);
    return $result;
};
$assertBuild = static function (array $arguments = []) use ($run, $expect): void {
    $result = $run('scripts/export-static.php', $arguments);
    $expect($result['status'] === 0 && $result['stderr'] === '', 'Build concluído: ' . $result['stderr']);
};

try {
    mkdir($workspace);
    foreach (['app', 'config', 'public', 'scripts', 'vendor'] as $directory) {
        $copyTree($root . '/' . $directory, $workspace . '/' . $directory);
    }
    file_put_contents($workspace . '/public/assets/images/nao-utilizado.svg', '<svg/>');
    file_put_contents($workspace . '/public/assets/config.php', '<?php echo "privado";');
    file_put_contents($workspace . '/public/assets/.env', 'SECRET=nao-publicar');
    $assertBuild();
    $baseline = $snapshot($workspace . '/dist');
    $expect(count($baseline) === 16, 'Inventário inicial tem 16 arquivos necessários');
    foreach (['index.html', 'projetos/index.html', 'projetos/gerenciador-de-permanencias/index.html', 'projetos/crowns/index.html', 'projetos/festividades-de-fim-de-ano/index.html', 'projetos/noticias/index.html', '404.html', 'sitemap.xml', 'robots.txt'] as $path) {
        $expect(isset($baseline[$path]), 'Arquivo público gerado: ' . $path);
    }
    foreach (array_keys($baseline) as $path) {
        $expect(!preg_match('~(?:portfolio-mvc-php|nao-utilizado|favicon\.svg|social-preview\.svg|\.gitkeep|\.php|\.env|^vendor/|^config/|^app/|^tests/|^scripts/)~', $path), 'Arquivo permitido: ' . $path);
    }
    $home = file_get_contents($workspace . '/dist/index.html');
    $expect(str_contains($home, 'href="/projetos/"') && str_contains($home, 'href="/projetos/crowns/"'), 'Links estáticos com barra final');
    $expect(str_contains($home, 'href="/assets/documents/CurriculoLinkedin.pdf" download>'), 'Download original preservado');
    $expect(str_contains($home, 'https://portfolio.example/') && str_contains($home, 'property="og:image"'), 'SEO renderizado no HTML');
    $error = file_get_contents($workspace . '/dist/404.html');
    $expect(str_contains($error, 'noindex, follow') && !str_contains($error, 'rel="canonical"') && !str_contains($error, 'property="og:'), '404 sem canonical ou OG e com noindex');
    $expect(!str_contains(file_get_contents($workspace . '/dist/sitemap.xml'), '404') && !str_contains($home, 'portfolio-mvc-php'), 'Erro e projeto oculto fora da descoberta');
    foreach (array_keys($baseline) as $path) {
        if (str_starts_with($path, 'assets/')) {
            $expect(hash_file('sha256', $workspace . '/dist/' . $path) === hash_file('sha256', $root . '/public/' . $path), 'Bytes do asset preservados: ' . $path);
        }
    }
    $assertBuild();
    $expect($snapshot($workspace . '/dist') === $baseline, 'Reconstrução idêntica sem alterações');

    foreach (['/Portifolio', '/outro/caminho'] as $base) {
        $environment['APP_BASE_PATH'] = $base;
        $environment['APP_ORIGIN'] = 'https://heitorsv001.github.io';
        $assertBuild(['--target=github']);
        $result = $run('scripts/validate-static.php', ['--target=github']);
        $expect($result['status'] === 0, 'Validação independente no prefixo ' . $base . ': ' . $result['stderr']);
        $html = file_get_contents($workspace . '/dist/index.html');
        $expect(str_contains($html, 'href="' . $base . '/projetos/crowns/"') && str_contains($html, 'href="' . $base . '/assets/documents/CurriculoLinkedin.pdf" download>'), 'Navegação e PDF com prefixo ' . $base);
        $expect(str_contains($html, 'https://heitorsv001.github.io/festividades/'), 'Demo externa no mesmo host preservada');
        $expect(is_file($workspace . '/dist/.nojekyll') && !is_dir($workspace . '/dist' . $base), 'Prefixo não cria pasta adicional e GitHub recebe .nojekyll');
    }
    $environment['APP_BASE_PATH'] = '';
    $environment['APP_ORIGIN'] = 'https://portfolio.example';
    $assertBuild();
    $expect(!is_file($workspace . '/dist/.nojekyll'), 'Troca para Cloudflare elimina arquivo específico do GitHub');

    $projectFile = $workspace . '/config/projects.php';
    $originalProjects = file_get_contents($projectFile);
    $projects = require $projectFile;
    $projects[2]['visible'] = true;
    file_put_contents($projectFile, '<?php return ' . var_export($projects, true) . ';');
    $assertBuild();
    $expect(is_file($workspace . '/dist/projetos/portfolio-mvc-php/index.html'), 'Fixture visível é exportada automaticamente');
    file_put_contents($projectFile, $originalProjects);
    $assertBuild();
    $expect(!is_dir($workspace . '/dist/projetos/portfolio-mvc-php'), 'Reconstrução após ocultação remove diretório antigo');
    $expect($snapshot($workspace . '/dist') === $baseline, 'Reconstrução após ocultação restaura inventário inicial');

    $assertFailure = static function (string $message, ?array $env = null, array $arguments = []) use ($run, $expect, $snapshot, $workspace, $baseline): void {
        $result = $run('scripts/export-static.php', $arguments, $env);
        $expect($result['status'] !== 0 && $result['stderr'] !== '', 'Erro interrompe exportação: ' . $message);
        $expect($snapshot($workspace . '/dist') === $baseline, 'Saída anterior preservada: ' . $message);
        $expect(glob($workspace . '/.static-build-*') === [] && glob($workspace . '/.static-backup-*') === [], 'Temporários removidos: ' . $message);
    };
    $invalid = $environment;
    unset($invalid['APP_BASE_PATH']);
    $assertFailure('prefixo ausente', $invalid);
    foreach (['http://portfolio.example', 'https://example.org/pasta', 'https://user:pass@example.org', 'https://example.org?x=1', ''] as $origin) {
        $assertFailure('origem inválida ' . $origin, array_replace($environment, ['APP_ORIGIN' => $origin]));
    }
    foreach (['portifolio', '/../segredo', '/duplo//prefixo', '/com espaco'] as $base) {
        $assertFailure('base inválida ' . $base, array_replace($environment, ['APP_BASE_PATH' => $base]));
    }
    $assertFailure('opção desconhecida', null, ['--output=/tmp']);
    $projects = require $projectFile;
    $projects[] = $projects[0];
    file_put_contents($projectFile, '<?php return ' . var_export($projects, true) . ';');
    $assertFailure('slug duplicado');
    $projects[count($projects) - 1]['slug'] = '../segredo';
    file_put_contents($projectFile, '<?php return ' . var_export($projects, true) . ';');
    $assertFailure('slug inválido');
    file_put_contents($projectFile, $originalProjects);

    $view = $workspace . '/app/Views/home/index.php';
    $originalView = file_get_contents($view);
    file_put_contents($view, '<?php throw new RuntimeException("falha de fixture"); ?>' . $originalView);
    $assertFailure('500 capturado pelo front controller');
    file_put_contents($view, '<?php trigger_error("aviso de fixture", E_USER_WARNING); ?>' . $originalView);
    $assertFailure('warning durante renderização');
    file_put_contents($view, $originalView . '<a href="/rota-ausente/">Link inválido</a>');
    $assertFailure('HTML com link quebrado');
    file_put_contents($view, $originalView);
    $cssFile = $workspace . '/public/assets/css/style.css';
    $originalCss = file_get_contents($cssFile);
    file_put_contents($cssFile, $originalCss . '\nbody { background-image: url(../images/ausente.png); }');
    $assertFailure('dependência CSS ausente');
    file_put_contents($cssFile, $originalCss);
    mkdir($workspace . '/public/assets/fonts');
    file_put_contents($workspace . '/public/assets/fonts/exemplo.woff2', 'fonte de fixture');
    file_put_contents($workspace . '/public/assets/css/importado.css', '@font-face { font-family: exemplo; src: url(../fonts/exemplo.woff2); }');
    file_put_contents($cssFile, $originalCss . '\n@import "importado.css";');
    $assertBuild();
    $expect(is_file($workspace . '/dist/assets/css/importado.css') && is_file($workspace . '/dist/assets/fonts/exemplo.woff2'), 'Dependências relativas de CSS e fontes são copiadas recursivamente');
    file_put_contents($cssFile, $originalCss);
    $assertBuild();
    $expect(!is_dir($workspace . '/dist/assets/fonts'), 'Reconstrução elimina fontes que deixaram de ser referenciadas');
    $portrait = $workspace . '/public/assets/images/meu-retrato.png';
    rename($portrait, $portrait . '.saved');
    symlink($portrait . '.saved', $portrait);
    $assertFailure('asset com link simbólico');
    unlink($portrait);
    rename($portrait . '.saved', $portrait);
    $lock = fopen($workspace . '/.static-export.lock', 'c');
    flock($lock, LOCK_EX);
    $assertFailure('exportação concorrente');
    fclose($lock);
    rename($workspace . '/dist', $workspace . '/dist.saved');
    symlink($workspace . '/dist.saved', $workspace . '/dist');
    $expect($run('scripts/export-static.php')['status'] !== 0 && is_link($workspace . '/dist'), 'Destino simbólico é recusado sem substituir o link');
    $expect($snapshot($workspace . '/dist.saved') === $baseline, 'Destino simbólico não permite alterar a árvore apontada');
    unlink($workspace . '/dist');
    file_put_contents($workspace . '/dist', 'arquivo alheio');
    $expect($run('scripts/export-static.php')['status'] !== 0 && file_get_contents($workspace . '/dist') === 'arquivo alheio', 'Arquivo no lugar de dist é preservado e interrompe o build');
    unlink($workspace . '/dist');
    rename($workspace . '/dist.saved', $workspace . '/dist');

    $dom = new DOMDocument();
    $dom->loadXML(file_get_contents($workspace . '/dist/sitemap.xml'));
    $expect($dom->getElementsByTagName('loc')->length === 6, 'Sitemap XML válido com seis páginas');

    $corruptions = [
        'canonical incorreto' => ['index.html', str_replace('rel="canonical" href="https://portfolio.example/"', 'rel="canonical" href="https://incorreto.example/"', $home)],
        'âncora inexistente' => ['index.html', str_replace('/#contato', '/#ausente', $home)],
        'metadescrição ausente' => ['index.html', preg_replace('~<meta name="description"[^>]+>~', '', $home)],
        'hash de asset incorreto' => ['index.html', preg_replace('/\?v=[a-f0-9]{12}/', '?v=000000000000', $home)],
        '404 sem noindex' => ['404.html', str_replace('noindex, follow', 'index, follow', $error)],
        'sitemap com projeto oculto' => ['sitemap.xml', str_replace('/projetos/crowns/', '/projetos/portfolio-mvc-php/', file_get_contents($workspace . '/dist/sitemap.xml'))],
        'PHP no artefato' => ['vazamento.php', '<?php echo "privado";'],
        'credencial no artefato' => ['.env', 'SECRET=nao-publicar'],
        'página oculta residual' => ['projetos/portfolio-mvc-php/index.html', $home],
        'código PHP em arquivo permitido' => ['assets/js/site.js', '<?php echo "privado";'],
    ];
    foreach ($corruptions as $message => [$relative, $contents]) {
        $file = $workspace . '/dist/' . $relative;
        $original = is_file($file) ? file_get_contents($file) : null;
        if (!is_dir(dirname($file))) {
            mkdir(dirname($file), 0777, true);
        }
        file_put_contents($file, $contents);
        $result = $run('scripts/validate-static.php');
        $expect($result['status'] !== 0 && $result['stderr'] !== '', 'Validador rejeita: ' . $message);
        if ($original === null) {
            unlink($file);
            if ($relative === 'projetos/portfolio-mvc-php/index.html') {
                rmdir(dirname($file));
            }
        } else {
            file_put_contents($file, $original);
        }
    }
    rename($workspace . '/dist/assets/js/site.js', $workspace . '/site.js.saved');
    $expect($run('scripts/validate-static.php')['status'] !== 0, 'Validador rejeita asset ausente');
    rename($workspace . '/site.js.saved', $workspace . '/dist/assets/js/site.js');
    $expect($run('scripts/validate-static.php')['status'] === 0, 'Artefato restaurado passa na validação');
} catch (Throwable $exception) {
    $checks++;
    $failures[] = $exception->getMessage();
} finally {
    if (is_dir($workspace)) {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($workspace, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($iterator as $entry) {
            if ($entry->isDir() && !$entry->isLink()) {
                rmdir($entry->getPathname());
            } else {
                unlink($entry->getPathname());
            }
        }
        rmdir($workspace);
    }
}

echo ($checks - count($failures)) . '/' . $checks . " verificações de exportação aprovadas.\n";
foreach ($failures as $failure) {
    fwrite(STDERR, 'FALHOU: ' . $failure . PHP_EOL);
}
exit($failures === [] ? 0 : 1);
