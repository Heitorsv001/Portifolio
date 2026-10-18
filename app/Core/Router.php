<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    /** @var array<string, callable> */
    private array $routes = [];

    public function __construct(private readonly string $basePath = '')
    {
    }

    public function get(string $path, callable $handler): void
    {
        $this->routes['GET ' . $this->normalizePath($path)] = $handler;
    }

    public function dispatch(string $method, string $uri): bool
    {
        $path = parse_url($uri, PHP_URL_PATH);
        if (!is_string($path) || $path === '' || !str_starts_with($path, '/')) {
            return false;
        }

        $basePath = rtrim($this->basePath, '/');
        if ($basePath !== '') {
            if ($path !== $basePath && !str_starts_with($path, $basePath . '/')) {
                return false;
            }
            $path = substr($path, strlen($basePath)) ?: '/';
        }

        $key = strtoupper($method) . ' ' . $this->normalizePath($path);
        if (!isset($this->routes[$key])) {
            return false;
        }

        ($this->routes[$key])();
        return true;
    }

    private function normalizePath(string $path): string
    {
        $path = '/' . trim($path, '/');
        return $path === '//' ? '/' : $path;
    }
}
