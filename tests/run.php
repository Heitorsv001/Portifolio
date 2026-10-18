<?php

declare(strict_types=1);

// Testes isolados: a integração do autoload é verificada separadamente com Composer.
require dirname(__DIR__) . '/app/Core/Controller.php';
require dirname(__DIR__) . '/app/Core/Router.php';
require dirname(__DIR__) . '/app/Controllers/HomeController.php';
require dirname(__DIR__) . '/app/Controllers/ErrorController.php';

use App\Controllers\ErrorController;
use App\Controllers\HomeController;
use App\Core\Router;

$failures = [];
$checks = 0;
$expect = static function (bool $condition, string $message) use (&$failures, &$checks): void {
    $checks++;
    if (!$condition) {
        $failures[] = $message;
    }
};

foreach (['', '/portifolio', '/outro/caminho/'] as $basePath) {
    $router = new Router($basePath);
    $calls = 0;
    $router->get('/', static function () use (&$calls): void {
        $calls++;
    });
    $root = rtrim($basePath, '/');

    $expect($router->dispatch('GET', $root . '/'), 'GET da raiz em ' . $basePath);
    $expect($router->dispatch('GET', $root . '/?origem=teste'), 'Query string em ' . $basePath);
    $expect(!$router->dispatch('GET', $root . '/inexistente'), 'Rota desconhecida em ' . $basePath);
    $expect(!$router->dispatch('POST', $root . '/'), 'POST não registrado em ' . $basePath);
    $expect(!$router->dispatch('GET', ''), 'URI vazia em ' . $basePath);
    $expect(!$router->dispatch('GET', 'sem-barra'), 'URI sem barra inicial em ' . $basePath);

    if ($root !== '') {
        $expect($router->dispatch('GET', $root), 'Raiz sem barra final em ' . $basePath);
        $expect(!$router->dispatch('GET', $root . '-extra/'), 'Limite do prefixo em ' . $basePath);
        $expect(!$router->dispatch('GET', '/'), 'Raiz fora do prefixo em ' . $basePath);
        $expect($calls === 3, 'Ações executadas apenas para rotas válidas em ' . $basePath);
    } else {
        $expect($calls === 2, 'Ações executadas apenas para rotas válidas na raiz');
    }
}

$router = new Router();
$router->get('/teste', static function (): void {});
$expect($router->dispatch('GET', '/teste/'), 'Normalização da barra final');
$expect(!$router->dispatch('GET', '/Teste'), 'Rotas distinguem maiúsculas e minúsculas');

foreach (['', '/portifolio'] as $basePath) {
    $config = ['name' => 'Teste <script>alert("x")</script>', 'base_path' => $basePath];
    $home = new HomeController($config);
    ob_start();
    $home->index();
    $html = ob_get_clean();
    $expect(str_contains($html, 'Estrutura MVC em PHP pronta para começar.'), 'Renderização da home');
    $expect(str_contains($html, '&lt;script&gt;'), 'Escaping do título');
    $expect(!str_contains($html, '<script>'), 'Ausência de HTML arbitrário no título');
    $expect(str_contains($html, 'href="' . $basePath . '/assets/css/style.css"'), 'URL do CSS com caminho-base');
}

$home = new HomeController(['name' => 'Teste', 'base_path' => '/teste" onclick="alert(1)']);
ob_start();
$home->index();
$html = ob_get_clean();
$expect(str_contains($html, '&quot; onclick=&quot;'), 'Escaping do atributo de stylesheet');

http_response_code(200);
ob_start();
(new ErrorController())->notFound();
$html = ob_get_clean();
$expect(http_response_code() === 404, 'Status HTTP 404');
$expect(str_contains($html, '<h1>Página não encontrada</h1>'), 'View de erro 404');
$expect(!str_contains($html, dirname(__DIR__)), '404 sem caminho interno');

$previousBasePath = getenv('APP_BASE_PATH');
// O valor padrão é verificado sem depender do ambiente que executa os testes.
putenv('APP_BASE_PATH');
$config = require dirname(__DIR__) . '/config/app.php';
$expect($config['base_path'] === '/portifolio', 'Caminho-base Apache preservado');
putenv('APP_BASE_PATH=');
$config = require dirname(__DIR__) . '/config/app.php';
$expect(rtrim($config['base_path'], '/') === '', 'Caminho-base vazio para raiz pública');
if ($previousBasePath === false) {
    putenv('APP_BASE_PATH');
} else {
    putenv('APP_BASE_PATH=' . $previousBasePath);
}

foreach ($failures as $failure) {
    fwrite(STDERR, 'FALHOU: ' . $failure . PHP_EOL);
}
echo ($checks - count($failures)) . '/' . $checks . ' verificações aprovadas.' . PHP_EOL;
exit($failures === [] ? 0 : 1);
