<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    /** @param array{name: string, base_path: string, origin?: ?string} $config */
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
        $homeUrl = $basePath . '/';
        $projectsUrl = $basePath . '/projetos';
        $stylesheetUrl = $basePath . '/assets/css/style.css';
        $data += [
            'title' => $appName,
            'description' => 'Portfólio de um Desenvolvedor Full Stack.',
            'currentPage' => '',
            'pagePath' => null,
            'noIndex' => false,
            'shareImageAlt' => 'Portfólio — Desenvolvedor Full Stack',
        ];
        $data['description'] = trim(preg_replace('/\s+/u', ' ', $data['description']) ?? '');
        $siteOrigin = $this->siteOrigin();
        $canonicalUrl = !$data['noIndex'] && $siteOrigin !== null && $data['pagePath'] !== null
            ? $siteOrigin . $basePath . $data['pagePath']
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
