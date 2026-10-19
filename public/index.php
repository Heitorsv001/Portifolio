<?php

declare(strict_types=1);

use App\Controllers\HomeController;
use App\Controllers\ErrorController;
use App\Controllers\ProjectController;
use App\Core\Router;
use App\Models\Contact;
use App\Models\Project;
use App\Models\Skill;

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
    $projects = new Project();
    $projectController = new ProjectController($config, $projects);
    $router->get('/', [new HomeController($config, $projects, new Skill(), new Contact()), 'index']);
    $router->get('/projetos', [$projectController, 'index']);
    $router->get('/projetos/{slug}', [$projectController, 'show']);

    if (!$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/')) {
        (new ErrorController($config))->notFound();
    }
} catch (Throwable $exception) {
    error_log((string) $exception);
    http_response_code(500);
    echo 'Não foi possível carregar a página.';
}
