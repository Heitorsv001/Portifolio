<?php

declare(strict_types=1);

namespace PortfolioStatic;

use DOMXPath;
use RuntimeException;

require_once __DIR__ . '/static-support.php';

function one(DOMXPath $xpath, string $query): string
{
    $nodes = $xpath->query($query);
    if ($nodes->length !== 1 || trim($nodes->item(0)->textContent) === '') {
        throw new RuntimeException('Elemento obrigatório ausente, vazio ou duplicado: ' . $query);
    }
    return trim($nodes->item(0)->textContent);
}

function validateArtifact(string $directory, array $settings, string $target): array
{
    if (!is_dir($directory) || is_link($directory)) {
        throw new RuntimeException('Diretório estático ausente ou inválido.');
    }
    $pages = pages();
    $files = $pages + ['/404.html' => '404.html'];
    $documents = [];
    $ids = [];
    foreach ($files as $route => $relative) {
        $file = $directory . '/' . $relative;
        if (!is_file($file) || is_link($file)) {
            throw new RuntimeException('Página ausente ou inválida: ' . $relative);
        }
        $html = file_get_contents($file);
        $dom = document($html);
        $xpath = new DOMXPath($dom);
        if (one($xpath, '/html/@lang') !== 'pt-BR' || $xpath->query('//base')->length !== 0
            || $xpath->query('/html/head')->length !== 1 || $xpath->query('/html/body')->length !== 1) {
            throw new RuntimeException('Estrutura de documento inválida em ' . $relative);
        }
        $title = one($xpath, '//head/title');
        $description = one($xpath, '//head/meta[@name="description"]/@content');
        one($xpath, '//h1');
        $ids[$route] = [];
        foreach ($xpath->query('//*[@id]') as $element) {
            $id = $element->getAttribute('id');
            if ($id === '' || isset($ids[$route][$id])) {
                throw new RuntimeException('ID vazio ou duplicado em ' . $relative);
            }
            $ids[$route][$id] = true;
        }
        foreach ($xpath->query('//*[@aria-labelledby]') as $element) {
            foreach (preg_split('/\s+/', $element->getAttribute('aria-labelledby')) as $id) {
                if (!isset($ids[$route][$id])) {
                    throw new RuntimeException('Heading referenciado não existe: ' . $id);
                }
            }
        }
        if ($route === '/404.html') {
            if (one($xpath, '//head/meta[@name="robots"]/@content') !== 'noindex, follow'
                || $xpath->query('//link[@rel="canonical"]|//meta[starts-with(@property, "og:")]')->length !== 0) {
                throw new RuntimeException('A página 404 deve omitir canonical/OG e manter noindex.');
            }
        } else {
            $canonical = $settings['origin'] . $settings['base'] . $route;
            if (one($xpath, '//head/link[@rel="canonical"]/@href') !== $canonical
                || one($xpath, '//head/meta[@property="og:url"]/@content') !== $canonical
                || one($xpath, '//head/meta[@property="og:title"]/@content') !== $title
                || one($xpath, '//head/meta[@property="og:description"]/@content') !== $description
                || one($xpath, '//head/meta[@property="og:type"]/@content') !== 'website'
                || one($xpath, '//head/meta[@property="og:locale"]/@content') !== 'pt_BR'
                || $xpath->query('//meta[@name="robots" and contains(@content, "noindex")]')->length !== 0) {
                throw new RuntimeException('SEO inconsistente em ' . $relative);
            }
            one($xpath, '//head/meta[@property="og:site_name"]/@content');
            $share = one($xpath, '//head/meta[@property="og:image"]/@content');
            if (!str_starts_with($share, $settings['origin'] . $settings['base'] . '/assets/images/')) {
                throw new RuntimeException('Imagem OG deve ser absoluta e pertencer aos assets publicados.');
            }
            $reference = localReference($share, $settings['base'] . $route, $settings);
            $size = getimagesize(assetFile($directory, ltrim($reference['path'], '/')));
            if ($size === false
                || one($xpath, '//head/meta[@property="og:image:width"]/@content') !== (string) $size[0]
                || one($xpath, '//head/meta[@property="og:image:height"]/@content') !== (string) $size[1]
                || one($xpath, '//head/meta[@property="og:image:type"]/@content') !== $size['mime']) {
                throw new RuntimeException('Dimensões ou tipo da imagem OG incorretos.');
            }
            one($xpath, '//head/meta[@property="og:image:alt"]/@content');
        }
        $documents[$route] = $html;
    }

    $assets = assets($documents, $settings, $directory);
    foreach ($assets as $path => $file) {
        if (hash_file('sha256', $file) !== hash_file('sha256', assetFile($settings['root'] . '/public', $path))) {
            throw new RuntimeException('Asset difere da fonte: ' . $path);
        }
    }
    foreach ($documents as $route => $html) {
        foreach (references(document($html)) as $url) {
            $reference = localReference($url, $settings['base'] . $route, $settings);
            if ($reference === null) {
                continue;
            }
            $path = $reference['path'];
            if (str_starts_with($path, '/assets/')) {
                $file = assetFile($directory, ltrim($path, '/'));
                parse_str($reference['query'], $query);
                if (isset($query['v']) && $query['v'] !== substr(hash_file('sha256', $file), 0, 12)) {
                    throw new RuntimeException('Versão de asset incorreta: ' . $url);
                }
            } elseif (!isset($files[$path])) {
                throw new RuntimeException('Link interno sem página exportada: ' . $url . ' em ' . $route);
            } elseif ($reference['fragment'] !== '' && !isset($ids[$path][$reference['fragment']])) {
                throw new RuntimeException('Âncora interna inexistente: ' . $url);
            }
        }
    }

    if (file_get_contents($directory . '/sitemap.xml') !== sitemap($pages, $settings)
        || file_get_contents($directory . '/robots.txt') !== robots($settings)) {
        throw new RuntimeException('Sitemap ou robots não corresponde às páginas e ao endereço deste build.');
    }
    $allowed = array_fill_keys(array_values($files), true) + array_fill_keys(array_keys($assets), true)
        + ['sitemap.xml' => true, 'robots.txt' => true];
    if ($target === 'github') {
        $allowed['.nojekyll'] = true;
        if (!is_file($directory . '/.nojekyll') || filesize($directory . '/.nojekyll') !== 0) {
            throw new RuntimeException('O perfil GitHub exige .nojekyll vazio.');
        }
    }
    $inventory = [];
    $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST);
    foreach ($iterator as $entry) {
        $relative = substr($entry->getPathname(), strlen($directory) + 1);
        if ($entry->isLink()) {
            throw new RuntimeException('Link simbólico no artefato: ' . $relative);
        }
        if ($entry->isDir()) {
            if (!array_filter(array_keys($allowed), static fn (string $path): bool => str_starts_with($path, $relative . '/'))) {
                throw new RuntimeException('Diretório não permitido no artefato: ' . $relative);
            }
            continue;
        }
        if (!isset($allowed[$relative]) || !$entry->isFile()) {
            throw new RuntimeException('Arquivo não permitido no artefato: ' . $relative);
        }
        if (preg_match('~<\?(?:php|=)~i', file_get_contents($entry->getPathname()))) {
            throw new RuntimeException('Código PHP encontrado no artefato: ' . $relative);
        }
        $inventory[$relative] = hash_file('sha256', $entry->getPathname());
    }
    if (count($inventory) !== count($allowed)) {
        throw new RuntimeException('Inventário de saída incompleto.');
    }
    ksort($inventory);
    return ['pages' => count($pages), 'assets' => count($assets), 'files' => $inventory];
}
