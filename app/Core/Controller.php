<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    /** @param array<string, mixed> $data */
    protected function view(string $name, array $data = []): void
    {
        $file = dirname(__DIR__) . '/Views/' . $name . '.php';
        if (!is_file($file)) {
            http_response_code(500);
            throw new \RuntimeException('View not found: ' . $name);
        }

        extract($data, EXTR_SKIP);
        require $file;
    }
}
