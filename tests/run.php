<?php

declare(strict_types=1);

// Testes isolados: a integração do autoload é verificada separadamente com Composer.
require dirname(__DIR__) . '/app/Core/Controller.php';
require dirname(__DIR__) . '/app/Core/Router.php';
require dirname(__DIR__) . '/app/Models/Project.php';
require dirname(__DIR__) . '/app/Models/Skill.php';
require dirname(__DIR__) . '/app/Controllers/HomeController.php';
require dirname(__DIR__) . '/app/Controllers/ProjectController.php';
require dirname(__DIR__) . '/app/Controllers/ErrorController.php';

use App\Controllers\ErrorController;
use App\Controllers\HomeController;
use App\Controllers\ProjectController;
use App\Core\Router;
use App\Models\Project;
use App\Models\Skill;

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

foreach (['', '/portifolio', '/outro/caminho/'] as $basePath) {
    $root = rtrim($basePath, '/');
    $router = new Router($basePath);
    $received = [];
    $router->get('/projetos/{slug}', static function (string $slug) use (&$received): void {
        $received[] = $slug;
    });
    $router->get('/projetos/fixo', static function () use (&$received): void {
        $received[] = 'rota fixa';
    });
    foreach (['/projetos/um-projeto', '/projetos/outro-projeto/?origem=teste', '/projetos/slug%2Dcodificado', '/projetos/%252F'] as $path) {
        $expect($router->dispatch('GET', $root . $path), 'Rota parametrizada encontrada: ' . $path);
    }
    $expect($received === ['um-projeto', 'outro-projeto', 'slug-codificado', '%2F'], 'Parâmetros capturados e decodificados uma única vez');
    $expect($router->dispatch('GET', $root . '/projetos/fixo'), 'Rota fixa após rota dinâmica encontrada');
    $expect(end($received) === 'rota fixa', 'Rota fixa tem precedência sobre parâmetro');
    $calls = count($received);
    foreach (['/projetos/', '/projetos//slug', '/projetos/slug/extra'] as $path) {
        $expect(!$router->dispatch('GET', $root . $path), 'Rota dinâmica exige um único segmento: ' . $path);
    }
    $expect(!$router->dispatch('POST', $root . '/projetos/slug'), 'Rota dinâmica não aceita POST');
    if ($root !== '') {
        $expect(!$router->dispatch('GET', $root . '-extra/projetos/slug'), 'Rota dinâmica respeita limite do prefixo');
    }
    $expect(count($received) === $calls, 'Rotas inválidas não executam handler dinâmico');
}

// Todas as páginas devem manter links e assets válidos na raiz ou em subdiretórios.
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
    $allowedLinks = [$basePath . '/', $basePath . '/projetos', $basePath . '/assets/css/style.css'];
    foreach ($ids[1] as $id) {
        $allowedLinks[] = '#' . $id;
    }
    foreach ($homeIds[1] as $id) {
        $allowedLinks[] = $basePath . '/#' . $id;
    }
    $expect(array_diff($links[1], $allowedLinks) === [], 'Links apontam apenas para destinos existentes');
};

$emptyDataFile = tempnam(sys_get_temp_dir(), 'portfolio-empty-');
file_put_contents($emptyDataFile, '<?php return [];');
$emptyProjects = new Project($emptyDataFile);
$emptySkills = new Skill($emptyDataFile);
unlink($emptyDataFile);

foreach (['', '/portifolio', '/outro/caminho/'] as $basePath) {
    $root = rtrim($basePath, '/');
    $config = ['name' => 'Teste <script>alert("x")</script>', 'base_path' => $basePath];
    $home = new HomeController($config, $emptyProjects, $emptySkills);
    ob_start();
    $home->index();
    $html = ob_get_clean();
    $homeHtml = $html;
    $expect(str_contains($html, '[Nome de exibição a informar]'), 'Nome pessoal pendente sinalizado');
    $expect(str_contains($html, 'Biografia em preparação.'), 'Biografia pendente sinalizada');
    $expect(str_contains($html, 'Objetivos profissionais a informar.'), 'Objetivos pendentes sinalizados');
    $expect(str_contains($html, 'Nenhum projeto em destaque'), 'Estado vazio dos destaques sem projetos inventados');
    $expect(str_contains($html, 'Habilidades a confirmar.'), 'Habilidades pendentes explicitamente sinalizadas');
    $expect(!str_contains($html, 'class="skill-category"'), 'Estado vazio não atribui habilidades ao proprietário');
    $expect(str_contains($html, 'href="' . $root . '/projetos"'), 'Home permite acessar catálogo');
    $expect(substr_count($html, '<h1 ') === 1, 'Título principal único na home');
    $expect(str_contains($html, 'Desenvolvedor <span class="hero-highlight">Full Stack</span>'), 'Posicionamento profissional confirmado');
    foreach (['sobre', 'objetivos', 'projetos', 'habilidades'] as $section) {
        $expect(str_contains($html, '<section id="' . $section . '"'), 'Seção disponível: ' . $section);
        if ($section !== 'projetos') {
            $expect(str_contains($html, 'href="' . $root . '/#' . $section . '"'), 'Navegação para a seção: ' . $section);
        }
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

    http_response_code(200);
    $catalog = new ProjectController($config, $emptyProjects);
    ob_start();
    $catalog->index();
    $html = ob_get_clean();
    $expect(http_response_code() === 200, 'Catálogo responde 200');
    $expect(str_contains($html, 'Nenhum projeto cadastrado.'), 'Catálogo vazio explícito');
    $expect(str_contains($html, '<title>Projetos — Teste &lt;script&gt;'), 'Título próprio escapado do catálogo');
    $expect(substr_count($html, '<h1>') === 1, 'Título principal único no catálogo');
    $expect(str_contains($html, 'href="' . $root . '/projetos" aria-current="page"'), 'Catálogo identificado na navegação');
    $expect(substr_count($html, 'aria-current="page"') === 1, 'Apenas catálogo marcado como página atual');
    $expectLayout($html, $root, $homeHtml);

    $router = new Router($basePath);
    $router->get('/projetos', [$catalog, 'index']);
    $router->get('/projetos/{slug}', [$catalog, 'show']);
    ob_start();
    $expect($router->dispatch('GET', $root . '/projetos/?origem=teste'), 'Rota do catálogo com barra final e query');
    ob_end_clean();
    $expect(!$router->dispatch('POST', $root . '/projetos'), 'Catálogo não aceita POST');
    ob_start();
    $expect($router->dispatch('GET', $root . '/projetos/exemplo'), 'Rota individual reconhecida mesmo sem item');
    $html = ob_get_clean();
    $expect(http_response_code() === 404, 'Detalhe inexistente retorna 404 com catálogo vazio');
    $expect(str_contains($html, 'Página não encontrada'), 'Detalhe inexistente usa view de erro');
}

// Dados fictícios isolados: editar o arquivo basta para atualizar catálogo e destaques.
$fixtureFile = tempnam(sys_get_temp_dir(), 'portfolio-projects-');
$imageFile = tempnam(dirname(__DIR__) . '/public/assets/images', 'test-project-');
rename($imageFile, $imageFile . '.png');
$imageFile .= '.png';
$imagePath = 'assets/images/' . basename($imageFile);
file_put_contents($imageFile, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+aBLsAAAAASUVORK5CYII='));

try {
    $fixture = [
        [
            'id' => 1,
            'slug' => 'teste-destaque',
            'title' => 'Teste <script>alert("x")</script>',
            'summary' => 'Resumo <img src=x onerror=alert(1)>',
            'description' => "Descrição completa <script>alert(1)</script>\nSegunda linha & detalhes.",
            'technologies' => ['PHP', '<b>HTML</b>'],
            'image' => $imagePath,
            'github_url' => 'https://example.org/repo?a=1&b=2',
            'demo_url' => 'https://example.org/demo',
            'featured' => true,
        ],
        [
            'id' => 2,
            'slug' => 'teste-sem-opcionais',
            'title' => 'Teste sem opcionais',
            'summary' => 'Somente campos obrigatórios.',
            'description' => 'Descrição isolada do teste.',
            'technologies' => [],
            'featured' => false,
        ],
    ];
    $writeFixture = static function (array $items) use ($fixtureFile): void {
        file_put_contents($fixtureFile, '<?php return ' . var_export($items, true) . ';');
    };
    $writeFixture([]);
    $expect((new Project($fixtureFile))->all() === [], 'Arquivo vazio produz catálogo vazio');
    $expect((new Project($fixtureFile))->featured() === [], 'Arquivo vazio produz destaques vazios');
    $writeFixture($fixture);
    $model = new Project($fixtureFile);
    $expect(count($model->all()) === 2, 'Model lê todos os itens do arquivo');
    $expect(array_column($model->featured(), 'id') === [1], 'Model seleciona apenas featured true');
    $expect($model->all()[1]['image'] === null && $model->all()[1]['demo_url'] === null, 'Campos opcionais ausentes normalizados');
    $expect($model->findBySlug('teste-destaque') === $model->all()[0], 'Consulta por slug retorna projeto correto normalizado');
    $expect($model->findBySlug('teste-sem-opcionais') === $model->all()[1], 'Consulta encontra projeto não destacado');
    foreach (['inexistente', '', 'Teste-destaque', '../teste-destaque', 'teste/destaque', 'teste--destaque', '-teste', 'teste-', 'teste_destaque', '%74este-destaque', "teste-destaque\n", "teste\0destaque", 'ação'] as $slug) {
        $expect($model->findBySlug($slug) === null, 'Slug desconhecido ou inválido não encontra projeto: ' . json_encode($slug));
    }

    foreach (['', '/portifolio', '/outro/caminho/'] as $basePath) {
        $root = rtrim($basePath, '/');
        $config = ['name' => 'Teste', 'base_path' => $basePath];
        ob_start();
        (new ProjectController($config, $model))->index();
        $html = ob_get_clean();
        $expect(substr_count($html, 'class="project-card"') === 2, 'Catálogo renderiza todos os cards');
        $expect(str_contains($html, 'Teste sem opcionais'), 'Projeto sem links e imagem exibido');
        $expect(str_contains($html, 'Imagem em preparação'), 'Fallback visual sem imagem');
        $expect(str_contains($html, 'src="' . $root . '/' . $imagePath . '"'), 'Imagem local respeita caminho-base');
        $expect(str_contains($html, 'alt="Prévia do projeto Teste &lt;script&gt;'), 'Alternativa textual escapada');
        $expect(str_contains($html, 'href="https://example.org/repo?a=1&amp;b=2"'), 'URL externa válida e escapada');
        $expect(str_contains($html, 'href="https://example.org/demo"'), 'Demonstração disponível');
        $expect(str_contains($html, '&lt;b&gt;HTML&lt;/b&gt;'), 'Tecnologias escapadas');
        $expect(str_contains($html, 'Resumo &lt;img src=x onerror=alert(1)&gt;'), 'Resumo escapado');
        $expect(!str_contains($html, '<script>') && !str_contains($html, '<img src=x'), 'Dados não injetam HTML');
        $expect(str_contains($html, 'href="' . $root . '/projetos/teste-destaque"'), 'Card aponta para detalhe pelo slug');
        $expect(str_contains($html, 'href="' . $root . '/projetos/teste-sem-opcionais"'), 'Projeto sem links externos tem detalhe acessível');

        $controller = new ProjectController($config, $model);
        $router = new Router($basePath);
        $router->get('/projetos/{slug}', [$controller, 'show']);
        foreach (['teste-destaque', 'teste-sem-opcionais'] as $slug) {
            $project = $model->findBySlug($slug);
            http_response_code(200);
            ob_start();
            $expect($router->dispatch('GET', $root . '/projetos/' . $slug . '/?origem=teste'), 'Detalhe despachado pelo Router');
            $detail = ob_get_clean();
            $expect(http_response_code() === 200, 'Projeto existente responde 200');
            $expect(str_contains($detail, '<title>' . htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8') . ' — Teste</title>'), 'Título específico e escapado do projeto');
            $expect(str_contains($detail, '<h1 id="projeto-titulo">' . htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8') . '</h1>'), 'H1 mostra título correto');
            $expect(str_contains($detail, htmlspecialchars($project['description'], ENT_QUOTES, 'UTF-8')), 'Descrição completa escapada preserva quebras');
            $expect(str_contains($detail, 'href="' . $root . '/projetos"'), 'Detalhe permite retornar ao catálogo');
            $expect(!str_contains($detail, '<script>') && !str_contains($detail, '<img src=x'), 'Detalhe não executa HTML dos dados');
            $expect(!str_contains($detail, 'aria-current="page"'), 'Detalhe não marca catálogo ou home como página atual');
            foreach (['<!doctype html>', '<header ', '<main ', '<footer ', '<h1 '] as $tag) {
                $expect(substr_count($detail, $tag) === 1, 'Detalhe reutiliza layout único: ' . $tag);
            }
            preg_match_all('/\bid="([^"]+)"/', $detail, $ids);
            preg_match_all('/aria-labelledby="([^"]+)"/', $detail, $labels);
            $expect(count($ids[1]) === count(array_unique($ids[1])), 'IDs únicos no detalhe');
            $expect(array_diff($labels[1], $ids[1]) === [], 'Headings referenciados existem no detalhe');
            if ($slug === 'teste-destaque') {
                $expect(str_contains($detail, 'src="' . $root . '/' . $imagePath . '"'), 'Detalhe mostra imagem local com prefixo');
                $expect(str_contains($detail, 'alt="Prévia do projeto Teste &lt;script&gt;'), 'Alt de imagem escapado no detalhe');
                $expect(str_contains($detail, '&lt;b&gt;HTML&lt;/b&gt;'), 'Tecnologias escapadas no detalhe');
                $expect(str_contains($detail, 'href="https://example.org/repo?a=1&amp;b=2"'), 'Link de repositório escapado no detalhe');
                $expect(str_contains($detail, 'href="https://example.org/demo"'), 'Link de demo disponível no detalhe');
                $expect(!str_contains($detail, '<h1 id="projeto-titulo">Teste sem opcionais'), 'Detalhe não mistura projetos');
            } else {
                $expect(!str_contains($detail, '<img '), 'Detalhe sem imagem não gera request quebrado');
                $expect(!str_contains($detail, 'class="project-technologies"'), 'Sem lista vazia de tecnologias');
                $expect(!str_contains($detail, 'id="projeto-links-titulo"'), 'Sem seção vazia de links');
                $expect(!str_contains($detail, 'https://example.org'), 'Detalhe sem opcionais não herda links de outro item');
            }
        }

        foreach (['desconhecido', 'Teste-destaque', '%2e%2e', 'teste%2Fdestaque', 'teste%252Fdestaque', 'teste%00destaque', 'teste-destaque%0A'] as $slug) {
            http_response_code(200);
            ob_start();
            $expect($router->dispatch('GET', $root . '/projetos/' . $slug), 'Router encaminha slug ao Controller');
            $detail = ob_get_clean();
            $expect(http_response_code() === 404, 'Slug inválido/desconhecido responde 404');
            $expect(str_contains($detail, '<h1>Página não encontrada</h1>'), 'Erro reutiliza view 404');
            $expect(!str_contains($detail, dirname(__DIR__)), '404 do detalhe não expõe caminho interno');
        }

        ob_start();
        (new HomeController($config, $model, $emptySkills))->index();
        $html = ob_get_clean();
        $expect(substr_count($html, 'class="project-card"') === 1, 'Home exibe somente destaque');
        $expect(!str_contains($html, 'Teste sem opcionais'), 'Home omite item não destacado');
        $expect(!str_contains($html, 'Nenhum projeto em destaque'), 'Estado vazio omitido com destaques');
        $expect(str_contains($html, 'href="' . $root . '/projetos/teste-destaque"'), 'Destaque na home aponta para página individual');
        $expect(str_contains($html, 'Habilidades a confirmar.') && !str_contains($html, 'class="skill-category"'), 'Tecnologias de projetos não são inferidas como habilidades pessoais');
    }

    $fixture[] = [
        'id' => 3, 'slug' => 'novo-item', 'title' => 'Novo item pelo arquivo',
        'summary' => 'Adicionado sem editar views.', 'description' => 'Teste.',
        'image' => 'assets/images/arquivo-inexistente.png',
        'github_url' => 'javascript:alert(1)', 'demo_url' => 'data:text/html,teste',
        'featured' => true,
    ];
    $writeFixture($fixture);
    $model = new Project($fixtureFile);
    $expect(count($model->all()) === 3 && count($model->featured()) === 2, 'Novo item no arquivo atualiza consultas');
    ob_start();
    (new ProjectController(['name' => 'Teste', 'base_path' => ''], $model))->index();
    $html = ob_get_clean();
    $expect(str_contains($html, 'Novo item pelo arquivo'), 'Novo item aparece sem editar views');
    $expect(!str_contains($html, 'javascript:') && !str_contains($html, 'data:text/html'), 'Links inseguros omitidos');
    $expect(!str_contains($html, 'arquivo-inexistente.png'), 'Imagem inexistente usa fallback');

    foreach (['', 'endereço inválido', '//example.org', 'ftp://example.org', 'https://user:pass@example.org'] as $url) {
        $fixture[0]['demo_url'] = $url;
        $writeFixture($fixture);
        $expect((new Project($fixtureFile))->all()[0]['demo_url'] === null, 'URL inválida ou não permitida omitida: ' . $url);
    }
    foreach (['https://example.org/image.png', 'assets/images/../../config/app.php', 'assets/css/style.css', null] as $image) {
        $fixture[0]['image'] = $image;
        $fixture[0]['technologies'] = null;
        $fixture[0]['github_url'] = null;
        $fixture[0]['demo_url'] = null;
        $fixture[0]['featured'] = false;
        $fixture[2]['featured'] = false;
        $writeFixture($fixture);
        $model = new Project($fixtureFile);
        $expect($model->all()[0]['image'] === null, 'Imagem ausente ou fora dos assets usa fallback');
        $expect($model->all()[0]['technologies'] === [], 'Tecnologias nulas usam lista vazia');
        ob_start();
        (new HomeController(['name' => 'Teste', 'base_path' => ''], $model, $emptySkills))->index();
        $html = ob_get_clean();
        $expect(str_contains($html, 'Nenhum projeto em destaque'), 'Home trata catálogo preenchido sem featured');
    }
} finally {
    unlink($fixtureFile);
    unlink($imageFile);
}

// Habilidades fictícias apenas em fixture: categorias e itens vêm do Model.
$skillFile = tempnam(sys_get_temp_dir(), 'portfolio-skills-');
try {
    $writeSkills = static function (array $groups) use ($skillFile): void {
        file_put_contents($skillFile, '<?php return ' . var_export($groups, true) . ';');
    };
    $writeSkills([]);
    $expect((new Skill($skillFile))->all() === [], 'Arquivo vazio produz habilidades vazias');
    $writeSkills([
        ['category' => 'Sem itens', 'items' => []],
        ['category' => 'Apenas espaços', 'items' => [' ', "\n"]],
        ['category' => ' ', 'items' => ['Não exibir']],
    ]);
    $expect((new Skill($skillFile))->all() === [], 'Categorias sem nome ou habilidades são omitidas');

    $groups = [
        ['category' => ' Linguagens de teste ', 'items' => [' PHP ', '', 'JavaScript']],
        ['category' => 'Ferramentas <script>alert("x")</script>', 'items' => ['Git & revisão', '<img src=x onerror=alert(1)>']],
        ['category' => 'Categoria vazia', 'items' => []],
    ];
    $writeSkills($groups);
    $skills = new Skill($skillFile);
    $expect($skills->all()[0] === ['category' => 'Linguagens de teste', 'items' => ['PHP', 'JavaScript']], 'Model preserva ordem, remove espaços e omite itens em branco');
    $expect(count($skills->all()) === 2, 'Somente categorias preenchidas são retornadas');

    foreach (['', '/portifolio', '/outro/caminho/'] as $basePath) {
        $root = rtrim($basePath, '/');
        $config = ['name' => 'Teste', 'base_path' => $basePath];
        http_response_code(200);
        ob_start();
        (new HomeController($config, $emptyProjects, $skills))->index();
        $html = ob_get_clean();
        $expect(http_response_code() === 200, 'Home com habilidades responde 200');
        $expect(substr_count($html, 'class="skill-category"') === 2, 'Home apresenta cada categoria preenchida');
        $expect(substr_count($html, 'class="skill-list"') === 2, 'Habilidades usam listas por categoria');
        $expect(str_contains($html, '<h3>Linguagens de teste</h3>'), 'Categoria usa heading abaixo do título da seção');
        $expect(str_contains($html, '<li>PHP</li>') && str_contains($html, '<li>JavaScript</li>'), 'Itens vêm do arquivo sem editar layout');
        $expect(str_contains($html, 'Ferramentas &lt;script&gt;alert(&quot;x&quot;)&lt;/script&gt;'), 'Nome da categoria escapado');
        $expect(str_contains($html, '<li>Git &amp; revisão</li>') && str_contains($html, '&lt;img src=x onerror=alert(1)&gt;'), 'Habilidades escapadas como texto');
        $expect(!str_contains($html, '<script>') && !str_contains($html, '<img src=x'), 'Dados de habilidades não injetam HTML');
        $expect(!str_contains($html, 'Habilidades a confirmar.') && !str_contains($html, 'Categoria vazia'), 'Sem aviso pendente ou categoria vazia quando há habilidades');
        $expect(str_contains($html, 'id="habilidades" class="home-section" aria-labelledby="habilidades-titulo" tabindex="-1"'), 'Seção tem heading associado e destino focável por fragmento');
        $expect(str_contains($html, 'href="' . $root . '/#habilidades"'), 'Menu acessa habilidades com caminho-base');
        $expect(strpos($html, 'id="habilidades"') > strpos($html, 'id="projetos"'), 'Seção de habilidades segue os projetos');
        preg_match_all('/\bid="([^"]+)"/', $html, $ids);
        preg_match_all('/aria-labelledby="([^"]+)"/', $html, $labels);
        $expect(array_diff($labels[1], $ids[1]) === [], 'Headings referenciados existem na home com habilidades');
        $expectLayout($html, $root, $html);

        foreach (['catalog', 'detail', 'error'] as $page) {
            ob_start();
            if ($page === 'error') {
                (new ErrorController($config))->notFound();
            } elseif ($page === 'detail') {
                (new ProjectController($config, $emptyProjects))->show('inexistente');
            } else {
                (new ProjectController($config, $emptyProjects))->index();
            }
            $otherHtml = ob_get_clean();
            $expect(str_contains($otherHtml, 'href="' . $root . '/#habilidades"'), 'Menu retorna à seção de habilidades a partir de ' . $page);
        }
    }

    $groups[] = ['category' => 'Nova categoria', 'items' => ['Nova habilidade de teste']];
    $writeSkills($groups);
    ob_start();
    (new HomeController(['name' => 'Teste', 'base_path' => ''], $emptyProjects, new Skill($skillFile)))->index();
    $html = ob_get_clean();
    $expect(str_contains($html, '<h3>Nova categoria</h3>') && str_contains($html, '<li>Nova habilidade de teste</li>'), 'Atualizar apenas o arquivo inclui categoria e habilidade na home');
} finally {
    unlink($skillFile);
}

$home = new HomeController(['name' => 'Teste', 'base_path' => '/teste" onclick="alert(1)'], $emptyProjects, $emptySkills);
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
