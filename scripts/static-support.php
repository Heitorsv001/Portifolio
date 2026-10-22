<?php

declare(strict_types=1);

namespace PortfolioStatic;

use App\Models\Project;
use DOMDocument;
use DOMXPath;
use RuntimeException;

function settings(): array
{
    if (PHP_SAPI !== 'cli' || PHP_VERSION_ID < 80200) {
        throw new RuntimeException('A exportação exige PHP CLI 8.2 ou superior.');
    }
    if (!class_exists(DOMDocument::class)) {
        throw new RuntimeException('Habilite a extensão DOM (ext-dom) no PHP CLI para validar a exportação.');
    }
    $root = dirname(__DIR__);
    if (!is_file($root . '/vendor/autoload.php')) {
        throw new RuntimeException('Autoload ausente. Execute composer install.');
    }
    require_once $root . '/vendor/autoload.php';

    $base = getenv('APP_BASE_PATH');
    $origin = getenv('APP_ORIGIN');
    if ($base === false || !preg_match('~^(?:/[a-zA-Z0-9_-]+)*/?$~D', $base)) {
        throw new RuntimeException('Defina APP_BASE_PATH explicitamente: vazio na raiz ou /prefixo, sem espaços ou segmentos especiais.');
    }
    $parts = $origin === false ? false : parse_url($origin);
    if ($origin === false || filter_var($origin, FILTER_VALIDATE_URL) === false || $parts === false
        || ($parts['scheme'] ?? '') !== 'https' || !isset($parts['host'])
        || isset($parts['user']) || isset($parts['pass'])
        || isset($parts['query']) || isset($parts['fragment'])
        || !in_array($parts['path'] ?? '', ['', '/'], true)) {
        throw new RuntimeException('APP_ORIGIN deve ser uma origem HTTPS, sem caminho, credenciais, query ou fragmento.');
    }

    return ['root' => $root, 'base' => rtrim($base, '/'), 'origin' => rtrim($origin, '/')];
}

function target(array $arguments): string
{
    if (count($arguments) > 1 || (isset($arguments[0]) && !in_array($arguments[0], ['--target=cloudflare', '--target=github'], true))) {
        throw new RuntimeException('Uso: php scripts/export-static.php [--target=cloudflare|--target=github]');
    }
    return ($arguments[0] ?? '--target=cloudflare') === '--target=github' ? 'github' : 'cloudflare';
}

function pages(): array
{
    $pages = ['/' => 'index.html', '/projetos/' => 'projetos/index.html'];
    foreach ((new Project())->all() as $project) {
        $slug = $project['slug'] ?? null;
        if (!is_string($slug) || !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/D', $slug)) {
            throw new RuntimeException('Slug público inválido; a exportação não renomeia projetos.');
        }
        $route = '/projetos/' . $slug . '/';
        if (isset($pages[$route])) {
            throw new RuntimeException('Slug público duplicado: ' . $slug);
        }
        $pages[$route] = 'projetos/' . $slug . '/index.html';
    }
    return $pages;
}

function document(string $html): DOMDocument
{
    if (!str_starts_with(strtolower(ltrim($html)), '<!doctype html>')
        || preg_match('~<\?(?:php|=)|(?:Warning|Fatal error|Notice):|Stack trace:~i', $html)) {
        throw new RuntimeException('HTML sem doctype ou com código/diagnóstico PHP.');
    }
    $previous = libxml_use_internal_errors(true);
    try {
        $dom = new DOMDocument();
        if (!$dom->loadHTML($html, LIBXML_NONET)) {
            throw new RuntimeException('HTML não pôde ser interpretado.');
        }
        return $dom;
    } finally {
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
    }
}

function cssReferences(string $css): array
{
    $css = preg_replace('~/\*.*?\*/~s', '', $css) ?? '';
    preg_match_all('~url\(\s*[\'"]?([^\s\'"()]+)[\'"]?\s*\)|@import\s+[\'"]([^\'"]+)[\'"]~i', $css, $matches, PREG_SET_ORDER);
    return array_map(static fn (array $match): string => $match[2] ?? $match[1], $matches);
}

function references(DOMDocument $dom): array
{
    $references = [];
    $xpath = new DOMXPath($dom);
    foreach ($xpath->query('//*[@href or @src or @srcset or @poster or @style]') as $element) {
        foreach (['href', 'src', 'poster'] as $attribute) {
            if ($element->hasAttribute($attribute)) {
                $references[] = $element->getAttribute($attribute);
            }
        }
        if ($element->hasAttribute('srcset')) {
            foreach (explode(',', $element->getAttribute('srcset')) as $candidate) {
                $references[] = preg_split('/\s+/', trim($candidate))[0];
            }
        }
        if ($element->hasAttribute('style')) {
            array_push($references, ...cssReferences($element->getAttribute('style')));
        }
    }
    foreach ($xpath->query('//style') as $style) {
        array_push($references, ...cssReferences($style->textContent));
    }
    foreach ($xpath->query('//meta[@property="og:image"]') as $meta) {
        $references[] = $meta->getAttribute('content');
    }
    return $references;
}

/** Resolve uma referência local para caminho lógico sem o prefixo de hospedagem. */
function localReference(string $url, string $page, array $settings): ?array
{
    if ($url === '' || preg_match('~[\x00-\x20\\\\]~', $url)) {
        throw new RuntimeException('Referência vazia ou inválida: ' . $url);
    }
    if (str_starts_with($url, '//')) {
        $url = 'https:' . $url;
    }
    $parts = parse_url($url);
    if ($parts === false) {
        throw new RuntimeException('URL inválida: ' . $url);
    }
    $absolute = isset($parts['scheme']);
    if ($absolute) {
        if (in_array(strtolower($parts['scheme']), ['mailto', 'tel', 'data'], true)) {
            return null;
        }
        if (!in_array(strtolower($parts['scheme']), ['http', 'https'], true) || !isset($parts['host'])
            || isset($parts['user']) || isset($parts['pass'])) {
            throw new RuntimeException('Esquema de URL não permitido: ' . $url);
        }
        $origin = parse_url($settings['origin']);
        if (strtolower($parts['scheme']) !== $origin['scheme']
            || strtolower($parts['host']) !== strtolower($origin['host'])
            || ($parts['port'] ?? 443) !== ($origin['port'] ?? 443)) {
            return null;
        }
    }
    $path = $parts['path'] ?? '';
    if ($path === '') {
        $path = $page;
    } elseif (!str_starts_with($path, '/')) {
        $path = substr($page, 0, (int) strrpos($page, '/') + 1) . $path;
    }
    $segments = [];
    $trailing = str_ends_with($path, '/');
    foreach (explode('/', rawurldecode($path)) as $segment) {
        if ($segment === '..') {
            if ($segments === []) {
                throw new RuntimeException('Caminho ultrapassa a raiz: ' . $url);
            }
            array_pop($segments);
        } elseif ($segment !== '' && $segment !== '.') {
            if (preg_match('~[\x00-\x20\\\\]~', $segment)) {
                throw new RuntimeException('Segmento de URL inválido: ' . $url);
            }
            $segments[] = $segment;
        }
    }
    $path = '/' . implode('/', $segments);
    if ($trailing && $path !== '/') {
        $path .= '/';
    }
    $base = $settings['base'];
    if ($base !== '' && $path !== $base && !str_starts_with($path, $base . '/')) {
        if ($absolute) {
            return null;
        }
        throw new RuntimeException('Link local fora de APP_BASE_PATH: ' . $url);
    }
    return [
        'path' => substr($path, strlen($base)) ?: '/',
        'fragment' => rawurldecode($parts['fragment'] ?? ''),
        'query' => $parts['query'] ?? '',
    ];
}

function assetFile(string $root, string $path): string
{
    $extensions = [
        'css' => 'css', 'js' => 'js', 'images' => 'png|jpe?g|webp|gif|avif|svg|ico',
        'documents' => 'pdf', 'fonts' => 'woff2?|ttf|otf|eot',
    ];
    $parts = explode('/', $path);
    $type = $parts[1] ?? '';
    if (!isset($extensions[$type])
        || !preg_match('~^assets/' . $type . '/(?:[a-zA-Z0-9_-]+/)*[a-zA-Z0-9_-][a-zA-Z0-9_.-]*\.(?:' . $extensions[$type] . ')$~D', $path)
        || preg_match('~(?:^|/)(?:test-|fixture-|\.\.?/)|\.php(?:\.|$)~i', $path)) {
        throw new RuntimeException('Asset fora da lista pública permitida: ' . $path);
    }
    $current = $root;
    foreach ($parts as $part) {
        $current .= '/' . $part;
        if (is_link($current)) {
            throw new RuntimeException('Link simbólico não permitido: ' . $path);
        }
    }
    if (!is_file($current) || !is_readable($current)) {
        throw new RuntimeException('Asset ausente ou sem leitura: ' . $path);
    }
    return $current;
}

/** Seleciona apenas recursos referenciados pelos HTMLs e dependências dos CSS. */
function assets(array $documents, array $settings, string $assetRoot): array
{
    $pending = [];
    foreach ($documents as $route => $html) {
        foreach (references(document($html)) as $url) {
            $reference = localReference($url, $settings['base'] . $route, $settings);
            if ($reference !== null && str_starts_with($reference['path'], '/assets/')) {
                $pending[] = ltrim($reference['path'], '/');
            }
        }
    }
    $assets = [];
    while ($pending !== []) {
        $path = array_pop($pending);
        if (isset($assets[$path])) {
            continue;
        }
        $file = assetFile($assetRoot, $path);
        $assets[$path] = $file;
        if (str_ends_with($path, '.css')) {
            foreach (cssReferences(file_get_contents($file)) as $url) {
                $reference = localReference($url, $settings['base'] . '/' . $path, $settings);
                if ($reference === null) {
                    continue;
                }
                if (!str_starts_with($reference['path'], '/assets/')) {
                    throw new RuntimeException('Dependência CSS fora de assets/: ' . $url);
                }
                $pending[] = ltrim($reference['path'], '/');
            }
        }
    }
    ksort($assets);
    return $assets;
}

function run(array $command): array
{
    $stdout = tmpfile();
    $stderr = tmpfile();
    if ($stdout === false || $stderr === false) {
        throw new RuntimeException('Não foi possível criar buffers temporários.');
    }
    try {
        $process = proc_open($command, [0 => ['pipe', 'r'], 1 => $stdout, 2 => $stderr], $pipes);
        if (!is_resource($process)) {
            throw new RuntimeException('Não foi possível executar o PHP de renderização.');
        }
        fclose($pipes[0]);
        $status = proc_close($process);
        rewind($stdout);
        rewind($stderr);
        return ['status' => $status, 'stdout' => stream_get_contents($stdout), 'stderr' => stream_get_contents($stderr)];
    } finally {
        fclose($stdout);
        fclose($stderr);
    }
}

function render(string $route, int $expected, array $settings): string
{
    $result = run([PHP_BINARY, $settings['root'] . '/scripts/render-static-page.php', $settings['base'] . $route]);
    if ($result['status'] !== 0 || $result['stderr'] !== '') {
        throw new RuntimeException('Renderização falhou em ' . $route . ': ' . trim($result['stderr']));
    }
    $page = json_decode($result['stdout'], true, 512, JSON_THROW_ON_ERROR);
    if (($page['status'] ?? null) !== $expected || !is_string($page['html'] ?? null) || trim($page['html']) === '') {
        throw new RuntimeException('Status ou HTML inesperado em ' . $route);
    }
    document($page['html']);
    return $page['html'];
}

function write(string $directory, string $relative, string $contents): void
{
    $file = $directory . '/' . $relative;
    if (!is_dir(dirname($file)) && !mkdir(dirname($file), 0777, true)) {
        throw new RuntimeException('Não foi possível criar diretório de saída.');
    }
    if (file_put_contents($file, $contents) !== strlen($contents)) {
        throw new RuntimeException('Gravação incompleta: ' . $relative);
    }
}

function sitemap(array $pages, array $settings): string
{
    $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
    foreach ($pages as $route => $_) {
        $xml .= '    <url><loc>' . htmlspecialchars($settings['origin'] . $settings['base'] . $route, ENT_XML1 | ENT_QUOTES, 'UTF-8') . "</loc></url>\n";
    }
    return $xml . "</urlset>\n";
}

function robots(array $settings): string
{
    return "User-agent: *\nAllow: /\n\nSitemap: " . $settings['origin'] . $settings['base'] . "/sitemap.xml\n";
}

function removeGenerated(string $directory): void
{
    $root = dirname(__DIR__);
    if (dirname($directory) !== $root || !preg_match('/^\.static-(?:build|backup)-[a-f0-9]+$/D', basename($directory)) || is_link($directory)) {
        throw new RuntimeException('Limpeza recusada fora da área gerada.');
    }
    if (!is_dir($directory)) {
        return;
    }
    $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($iterator as $entry) {
        if ($entry->isDir() && !$entry->isLink()) {
            rmdir($entry->getPathname());
        } else {
            unlink($entry->getPathname());
        }
    }
    rmdir($directory);
}

function strictErrors(): void
{
    error_reporting(E_ALL);
    set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
        if (!(error_reporting() & $severity)) {
            return false;
        }
        throw new \ErrorException($message, 0, $severity, $file, $line);
    });
}
