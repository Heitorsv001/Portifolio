<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require __DIR__ . '/static-validation.php';

if ($argc === 2 && $argv[1] === '--help') {
    echo "Defina APP_BASE_PATH (vazio na raiz) e APP_ORIGIN (origem HTTPS).\n";
    echo "Uso: php scripts/export-static.php [--target=cloudflare|--target=github]\n";
    exit;
}

$temporary = null;
$backup = null;
$lock = null;
$exitCode = 0;
try {
    \PortfolioStatic\strictErrors();
    $target = \PortfolioStatic\target(array_slice($argv, 1));
    $settings = \PortfolioStatic\settings();
    $root = $settings['root'];
    foreach ([$root . '/dist', $root . '/.static-export.lock'] as $path) {
        if (is_link($path) || (file_exists($path) && ($path === $root . '/dist' ? !is_dir($path) : !is_file($path)))) {
            throw new RuntimeException('Destino ou lock inválido: ' . basename($path));
        }
    }
    $lock = fopen($root . '/.static-export.lock', 'c');
    if ($lock === false || !flock($lock, LOCK_EX | LOCK_NB)) {
        throw new RuntimeException('Outra exportação está em execução.');
    }
    $pages = \PortfolioStatic\pages();
    $temporary = $root . '/.static-build-' . bin2hex(random_bytes(8));
    if (!mkdir($temporary)) {
        throw new RuntimeException('Não foi possível criar a saída temporária.');
    }
    $documents = [];
    foreach ($pages as $route => $file) {
        $documents[$route] = \PortfolioStatic\render($route, 200, $settings);
        \PortfolioStatic\write($temporary, $file, $documents[$route]);
    }
    $documents['/404.html'] = \PortfolioStatic\render('/__static_export_404__/', 404, $settings);
    \PortfolioStatic\write($temporary, '404.html', $documents['/404.html']);
    foreach (\PortfolioStatic\assets($documents, $settings, $root . '/public') as $path => $file) {
        \PortfolioStatic\write($temporary, $path, file_get_contents($file));
    }
    \PortfolioStatic\write($temporary, 'sitemap.xml', \PortfolioStatic\sitemap($pages, $settings));
    \PortfolioStatic\write($temporary, 'robots.txt', \PortfolioStatic\robots($settings));
    if ($target === 'github') {
        \PortfolioStatic\write($temporary, '.nojekyll', '');
    }
    $result = \PortfolioStatic\validateArtifact($temporary, $settings, $target);
    if (is_dir($root . '/dist')) {
        $backup = $root . '/.static-backup-' . bin2hex(random_bytes(8));
        if (!rename($root . '/dist', $backup)) {
            throw new RuntimeException('Não foi possível preservar a saída anterior.');
        }
    }
    if (!rename($temporary, $root . '/dist')) {
        throw new RuntimeException('Não foi possível concluir a substituição de dist/.');
    }
    $temporary = null;
    if ($backup !== null) {
        \PortfolioStatic\removeGenerated($backup);
        $backup = null;
    }
    echo 'Exportação validada em dist/: ' . $result['pages'] . ' páginas públicas, 404 e '
        . $result['assets'] . ' assets; ' . count($result['files']) . " arquivos. Nenhuma publicação realizada.\n";
} catch (Throwable $exception) {
    if ($backup !== null && !file_exists(dirname(__DIR__) . '/dist')) {
        rename($backup, dirname(__DIR__) . '/dist');
        $backup = null;
    }
    fwrite(STDERR, 'Exportação interrompida: ' . $exception->getMessage() . PHP_EOL);
    $exitCode = 1;
} finally {
    if ($temporary !== null) {
        \PortfolioStatic\removeGenerated($temporary);
    }
    if (is_resource($lock)) {
        fclose($lock);
    }
}

exit($exitCode);
