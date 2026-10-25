<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\Contact;

abstract class Controller
{
    /** @param array{name: string, base_path: string, origin?: ?string, static_export?: bool} $config */
    public function __construct(protected readonly array $config)
    {
    }

    /** @param array<string, mixed> $data */
    protected function view(string $name, array $data = []): void
    {
        $file = dirname(__DIR__) . '/Views/' . $name . '.php';
        if (!is_file($file)) {
            http_response_code(500);
            throw new \RuntimeException('View not found: ' . $name);
        }

        $appName = $this->config['name'];
        $basePath = rtrim($this->config['base_path'], '/');
        $pageUrl = fn (string $path): string => $this->pageUrl($path);
        $homeUrl = $pageUrl('/');
        $projectsUrl = $pageUrl('/projetos');
        $stylesheetUrl = $this->versionedAssetUrl('assets/css/style.css');
        $themeScriptUrl = $this->versionedAssetUrl('assets/js/theme-init.js');
        $siteScriptUrl = $this->versionedAssetUrl('assets/js/site.js');
        $faviconUrl = $basePath . '/assets/images/favicon.svg';
        $data += [
            'title' => $appName,
            'description' => 'Portfólio de um Desenvolvedor Full Stack.',
            'currentPage' => '',
            'pagePath' => null,
            'noIndex' => false,
            'shareImageAlt' => 'Portfólio — Desenvolvedor Full Stack',
        ];
        $data['contact'] ??= (new Contact())->all();
        $data['description'] = trim(preg_replace('/\s+/u', ' ', $data['description']) ?? '');
        $particlesScriptUrl = $data['currentPage'] === 'home'
            ? $this->versionedAssetUrl('assets/js/hero-particles.js')
            : null;
        $siteOrigin = $this->siteOrigin();
        $canonicalUrl = !$data['noIndex'] && $siteOrigin !== null && $data['pagePath'] !== null
            ? $siteOrigin . $pageUrl($data['pagePath'])
            : null;
        $shareImageUrl = null;
        $shareImageSize = false;
        if ($canonicalUrl !== null) {
            $shareImagePath = $data['shareImage'] ?? 'assets/images/social-preview.png';
            $shareImageSize = @getimagesize(dirname(__DIR__, 2) . '/public/' . $shareImagePath);
            if ($shareImageSize !== false) {
                $shareImageUrl = $siteOrigin . $basePath . '/' . $shareImagePath;
            }
        }

        extract($data, EXTR_SKIP);
        require dirname(__DIR__) . '/Views/layouts/main.php';
    }

    private function pageUrl(string $path): string
    {
        if ($this->config['static_export'] ?? false) {
            $path = rtrim($path, '/') . '/';
        }

        return rtrim($this->config['base_path'], '/') . $path;
    }

    private function versionedAssetUrl(string $path): string
    {
        $file = dirname(__DIR__, 2) . '/public/' . $path;
        $version = is_file($file) ? hash_file('sha256', $file) : false;

        return rtrim($this->config['base_path'], '/') . '/' . $path
            . ($version === false ? '' : '?v=' . substr($version, 0, 12));
    }

    private function siteOrigin(): ?string
    {
        $origin = $this->config['origin'] ?? null;
        if ($origin === null || filter_var($origin, FILTER_VALIDATE_URL) === false) {
            return null;
        }

        $parts = parse_url($origin);
        if (!in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true)
            || isset($parts['user']) || isset($parts['pass'])
            || isset($parts['query']) || isset($parts['fragment'])
            || !in_array($parts['path'] ?? '', ['', '/'], true)) {
            return null;
        }

        return rtrim($origin, '/');
    }
}
