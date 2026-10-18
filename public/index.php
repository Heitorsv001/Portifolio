<?php

declare(strict_types=1);

use App\Controllers\HomeController;
use App\Controllers\ErrorController;
use App\Core\Router;

ini_set('display_errors', '0');
header('Content-Type: text/html; charset=UTF-8');

try {
    $autoload = dirname(__DIR__) . '/vendor/autoload.php';
    if (!is_file($autoload)) {
        throw new RuntimeException('Autoload ausente. Execute composer install na raiz do projeto.');
    }

    require $autoload;
    $config = require dirname(__DIR__) . '/config/app.php';

    $router = new Router($config['base_path']);
    $router->get('/', [new HomeController($config), 'index']);

    if (!$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/')) {
        (new ErrorController())->notFound();
    }
} catch (Throwable $exception) {
    error_log((string) $exception);
    http_response_code(500);
    echo 'Não foi possível carregar a página.';
}
