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

// Ambas as páginas devem manter links e assets válidos na raiz ou em subdiretórios.
$expectLayout = static function (string $html, string $basePath, string $homeHtml) use ($expect): void {
    foreach (['<!doctype html>', '<html ', '<head>', '<body ', '<header ', '<main ', '<footer '] as $tag) {
        $expect(substr_count($html, $tag) === 1, 'Layout único: ' . $tag . ' em ' . $basePath);
    }
    $expect(str_contains($html, 'href="' . $basePath . '/assets/css/style.css"'), 'CSS compartilhado em ' . $basePath);
    $expect(str_contains($html, 'href="#conteudo"'), 'Link para pular navegação');
    $expect(str_contains($html, 'id="conteudo"'), 'Destino do link para conteúdo');
    $expect(str_contains($html, 'href="#topo"') && str_contains($html, 'id="topo"'), 'Navegação para o topo');

    preg_match_all('/href="([^"]+)"/', $html, $links);
    preg_match_all('/\bid="([^"]+)"/', $html, $ids);
    preg_match_all('/\bid="([^"]+)"/', $homeHtml, $homeIds);
    $expect(count($ids[1]) === count(array_unique($ids[1])), 'Identificadores únicos na página');
    $allowedLinks = [$basePath . '/', $basePath . '/assets/css/style.css'];
    foreach ($ids[1] as $id) {
        $allowedLinks[] = '#' . $id;
    }
    foreach ($homeIds[1] as $id) {
        $allowedLinks[] = $basePath . '/#' . $id;
    }
    $expect(array_diff($links[1], $allowedLinks) === [], 'Links apontam apenas para destinos existentes');
};

foreach (['', '/portifolio', '/outro/caminho/'] as $basePath) {
    $root = rtrim($basePath, '/');
    $config = ['name' => 'Teste <script>alert("x")</script>', 'base_path' => $basePath];
    $home = new HomeController($config);
    ob_start();
    $home->index();
    $html = ob_get_clean();
    $homeHtml = $html;
    $expect(str_contains($html, '[Nome de exibição a informar]'), 'Nome pessoal pendente sinalizado');
    $expect(str_contains($html, 'Biografia em preparação.'), 'Biografia pendente sinalizada');
    $expect(str_contains($html, 'Objetivos profissionais a informar.'), 'Objetivos pendentes sinalizados');
    $expect(str_contains($html, 'Projetos em preparação'), 'Projetos futuros sinalizados sem catálogo');
    $expect(substr_count($html, '<h1 ') === 1, 'Título principal único na home');
    $expect(str_contains($html, 'Desenvolvedor <span class="hero-highlight">Full Stack</span>'), 'Posicionamento profissional confirmado');
    foreach (['sobre', 'objetivos', 'projetos'] as $section) {
        $expect(str_contains($html, '<section id="' . $section . '"'), 'Seção disponível: ' . $section);
        $expect(str_contains($html, 'href="' . $root . '/#' . $section . '"'), 'Navegação para a seção: ' . $section);
    }
    $expect(str_contains($html, 'class="button" href="#projetos"'), 'Chamada principal leva à seção de projetos');
    $expect(str_contains($html, 'class="button button--secondary" href="#sobre"'), 'Chamada secundária leva à apresentação');
    $expect(str_contains($html, '&lt;script&gt;'), 'Escaping do título');
    $expect(!str_contains($html, '<script>'), 'Ausência de HTML arbitrário no título');
    $expectLayout($html, $root, $homeHtml);
    $expect(str_contains($html, 'aria-current="page"'), 'Home identificada na navegação');
    $expect(str_contains($html, '<span>Teste &lt;script&gt;alert(&quot;x&quot;)&lt;/script&gt;</span>'), 'Nome escapado no header');

    http_response_code(200);
    ob_start();
    (new ErrorController($config))->notFound();
    $html = ob_get_clean();
    $expect(http_response_code() === 404, 'Status HTTP 404 em ' . $basePath);
    $expect(str_contains($html, '<h1>Página não encontrada</h1>'), 'View de erro 404');
    $expect(str_contains($html, '<title>Página não encontrada</title>'), 'Título próprio da página 404');
    $expect(!str_contains($html, dirname(__DIR__)), '404 sem caminho interno');
    $expect(!str_contains($html, 'aria-current="page"'), '404 não marca home como página atual');
    $expect(str_contains($html, 'class="button" href="' . $root . '/"'), '404 permite retornar ao início');
    $expect(!str_contains($html, '<script>'), 'Nome da aplicação escapado também na página 404');
    $expectLayout($html, $root, $homeHtml);
}

$home = new HomeController(['name' => 'Teste', 'base_path' => '/teste" onclick="alert(1)']);
ob_start();
$home->index();
$html = ob_get_clean();
$expect(str_contains($html, 'href="/teste&quot; onclick=&quot;alert(1)/assets/css/style.css"'), 'Escaping do atributo de stylesheet');
$expect(str_contains($html, 'href="/teste&quot; onclick=&quot;alert(1)/"'), 'Escaping do caminho-base na navegação');

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
