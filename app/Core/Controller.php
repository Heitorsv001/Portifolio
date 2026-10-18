<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    /** @param array{name: string, base_path: string} $config */
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
        $stylesheetUrl = $basePath . '/assets/css/style.css';
        $data += ['title' => $appName, 'currentPage' => ''];

        extract($data, EXTR_SKIP);
        require dirname(__DIR__) . '/Views/layouts/main.php';
    }
}
