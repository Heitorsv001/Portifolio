<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    /** @var array<string, callable> */
    private array $routes = [];

    /** @var list<array{pattern: string, handler: callable}> */
    private array $parameterizedRoutes = [];

    public function __construct(private readonly string $basePath = '')
    {
    }

    public function get(string $path, callable $handler): void
    {
        $path = $this->normalizePath($path);
        $segments = explode('/', $path);
        $parameterized = false;

        foreach ($segments as &$segment) {
            if (preg_match('/^\{[a-zA-Z_][a-zA-Z0-9_]*\}$/D', $segment)) {
                $segment = '([^/]+)';
                $parameterized = true;
            } else {
                $segment = preg_quote($segment, '~');
            }
        }
        unset($segment);

        if ($parameterized) {
            $this->parameterizedRoutes[] = [
                'pattern' => '~^' . implode('/', $segments) . '$~D',
                'handler' => $handler,
            ];
            return;
        }

        $this->routes['GET ' . $path] = $handler;
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

        $path = $this->normalizePath($path);
        $key = strtoupper($method) . ' ' . $path;
        if (isset($this->routes[$key])) {
            ($this->routes[$key])();
            return true;
        }

        if (strtoupper($method) === 'GET') {
            foreach ($this->parameterizedRoutes as $route) {
                if (preg_match($route['pattern'], $path, $matches)) {
                    ($route['handler'])(...array_map('rawurldecode', array_slice($matches, 1)));
                    return true;
                }
            }
        }

        return false;
    }

    private function normalizePath(string $path): string
    {
        $path = '/' . trim($path, '/');
        return $path === '//' ? '/' : $path;
    }
}
