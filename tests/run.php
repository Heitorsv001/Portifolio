<?php

declare(strict_types=1);

// Testes isolados: a integração do autoload é verificada separadamente com Composer.
require dirname(__DIR__) . '/app/Core/Controller.php';
require dirname(__DIR__) . '/app/Core/Router.php';
require dirname(__DIR__) . '/app/Models/Project.php';
require dirname(__DIR__) . '/app/Models/Skill.php';
require dirname(__DIR__) . '/app/Models/Contact.php';
require dirname(__DIR__) . '/app/Models/Profile.php';
require dirname(__DIR__) . '/app/Controllers/HomeController.php';
require dirname(__DIR__) . '/app/Controllers/ProjectController.php';
require dirname(__DIR__) . '/app/Controllers/ErrorController.php';

use App\Controllers\ErrorController;
use App\Controllers\HomeController;
use App\Controllers\ProjectController;
use App\Core\Router;
use App\Models\Project;
use App\Models\Skill;
use App\Models\Contact;
use App\Models\Profile;

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

$expectFooter = static function (string $html, array $contact) use ($expect): void {
    preg_match('~<footer class="site-footer">(.*?)</footer>~s', $html, $match);
    $footer = $match[1] ?? '';
    $expect($footer !== '' && str_contains($footer, 'Desenvolvedor Web Full Stack'), 'Rodapé mantém identificação profissional');
    $networks = ['github_url' => 'GitHub', 'linkedin_url' => 'LinkedIn'];
    $count = 0;
    foreach ($networks as $key => $label) {
        if ($contact[$key] === null) {
            $expect(!str_contains($footer, 'aria-label="Perfil no ' . $label . '"'), 'Rede ausente não gera link no rodapé: ' . $label);
            continue;
        }
        $count++;
        $expect(str_contains($footer, 'href="' . htmlspecialchars($contact[$key], ENT_QUOTES, 'UTF-8') . '" aria-label="Perfil no ' . $label . '"'), 'Rodapé usa rede normalizada, escapada e nomeada: ' . $label);
    }
    $expect(substr_count($footer, 'class="social-links"') === ($count > 0 ? 1 : 0), 'Rodapé omite lista de redes quando vazia');
    $expect(substr_count($footer, '<a ') === $count + 1, 'Rodapé contém somente redes disponíveis e retorno ao topo');
    $expect(str_contains($footer, 'href="#topo"') && str_contains($html, 'id="topo"'), 'Rodapé retorna ao topo da própria página');
    $expect(!str_contains($footer, 'mailto:') && !str_contains($footer, ' download') && !str_contains($footer, 'Contatos a confirmar.'), 'Rodapé sem e-mail, currículo ou aviso de contatos pendentes');
};

// Todas as páginas devem manter links e assets válidos na raiz ou em subdiretórios.
$expectLayout = static function (string $html, string $basePath, string $homeHtml, ?array $contact = null) use ($expect, $expectFooter): void {
    $contact ??= (new Contact())->all();
    $expectFooter($html, $contact);
    foreach (['<!doctype html>', '<html ', '<head>', '<body ', '<header ', '<main ', '<footer '] as $tag) {
        $expect(substr_count($html, $tag) === 1, 'Layout único: ' . $tag . ' em ' . $basePath);
    }
    $stylesheetUrl = $basePath . '/assets/css/style.css?v=' . substr(hash_file('sha256', dirname(__DIR__) . '/public/assets/css/style.css'), 0, 12);
    $themeScriptUrl = $basePath . '/assets/js/theme-init.js?v=' . substr(hash_file('sha256', dirname(__DIR__) . '/public/assets/js/theme-init.js'), 0, 12);
    $siteScriptUrl = $basePath . '/assets/js/site.js?v=' . substr(hash_file('sha256', dirname(__DIR__) . '/public/assets/js/site.js'), 0, 12);
    $expect(str_contains($html, 'href="' . $stylesheetUrl . '"'), 'CSS compartilhado e versão do conteúdo em ' . $basePath);
    $themeScript = '<script src="' . $themeScriptUrl . '"></script>';
    $siteScript = '<script src="' . $siteScriptUrl . '" defer></script>';
    $expect(str_contains($html, $themeScript), 'Inicialização de tema respeita caminho-base');
    $expect(str_contains($html, $siteScript), 'Interações carregadas com defer e caminho-base');
    $expect(strpos($html, $themeScript) < strpos($html, '<link rel="stylesheet"'), 'Tema inicializado antes do CSS');
    $expect(substr_count($html, 'data-theme-toggle') === 1, 'Um controle de tema por página');
    $expect(str_contains($html, 'aria-label="Alternar para tema claro" title="Alternar para tema claro" hidden'), 'Controle de tema nomeado e oculto até o JS estar disponível');
    $expect(str_contains($html, '<link rel="icon" type="image/png" href="' . $basePath . '/assets/images/meu-retrato.png">'), 'Retrato como favicon PNG respeita caminho-base em ' . $basePath);
    $expect(str_contains($html, 'href="#conteudo"'), 'Link para pular navegação');
    $expect(str_contains($html, 'id="conteudo"'), 'Destino do link para conteúdo');
    $expect(str_contains($html, '<html lang="pt-BR"'), 'Idioma da página declarado');
    $expect(!preg_match('/tabindex="[1-9]/', $html), 'Sem ordem de foco artificial por tabindex positivo');
    $expect(str_contains($html, '<ul class="nav-list" role="list">'), 'Semântica da lista de navegação preservada');
    $expect(substr_count($html, 'data-menu-toggle') === 1, 'Um acionador de menu por página');
    $expect(str_contains($html, 'aria-expanded="false" aria-controls="navegacao-principal" aria-label="Abrir menu" hidden'), 'Menu recolhido e acionador oculto antes do JS');
    $expect(str_contains($html, '<nav id="navegacao-principal" class="site-navigation" aria-label="Navegação principal">'), 'Navegação permanece visível sem JS');
    foreach (['experiencia', 'sobre', 'projetos', 'habilidades', 'formacao', 'contato'] as $section) {
        $expect(str_contains($html, 'href="' . $basePath . '/#' . $section . '"'), 'Menu aponta para seção da home: ' . $section);
    }
    $expect(str_contains($html, 'href="#topo"') && str_contains($html, 'id="topo"'), 'Navegação para o topo');
    if (str_contains($html, 'class="home-hero"')) {
        $expect(str_contains($html, 'class="site-main site-main--home"'), 'Home permite hero com largura própria');
        $particlesUrl = $basePath . '/assets/js/hero-particles.js?v=' . substr(hash_file('sha256', dirname(__DIR__) . '/public/assets/js/hero-particles.js'), 0, 12);
        $expect(str_contains($html, '<script src="' . $particlesUrl . '" defer></script>'), 'Partículas carregadas apenas na home, com caminho-base e versão');
        $expect(str_contains($html, '<canvas class="hero-particles" data-hero-particles aria-hidden="true"></canvas>'), 'Canvas decorativo não recebe foco nem conteúdo semântico');
    } else {
        $expect(str_contains($html, 'class="site-main container"'), 'Páginas internas mantêm contêiner próprio');
        $expect(!str_contains($html, 'hero-particles'), 'Páginas internas não carregam efeito de partículas');
    }

    preg_match_all('/href="([^"]+)"/', $html, $links);
    preg_match_all('/\bid="([^"]+)"/', $html, $ids);
    preg_match_all('/\bid="([^"]+)"/', $homeHtml, $homeIds);
    $expect(count($ids[1]) === count(array_unique($ids[1])), 'Identificadores únicos na página');
    $allowedLinks = [$basePath . '/', $basePath . '/projetos', $stylesheetUrl, $basePath . '/assets/images/meu-retrato.png'];
    foreach (['github_url', 'linkedin_url'] as $key) {
        if ($contact[$key] !== null) {
            $allowedLinks[] = htmlspecialchars($contact[$key], ENT_QUOTES, 'UTF-8');
        }
    }
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
$emptyContact = new Contact($emptyDataFile);
unlink($emptyDataFile);

foreach (['', '/portifolio', '/outro/caminho/'] as $basePath) {
    $root = rtrim($basePath, '/');
    $config = ['name' => 'Teste <script>alert("x")</script>', 'base_path' => $basePath];
    $home = new HomeController($config, $emptyProjects, $emptySkills, $emptyContact);
    ob_start();
    $home->index();
    $html = ob_get_clean();
    $homeHtml = $html;
    $expect(str_contains($html, 'Heitor Silva Vareiro'), 'Nome profissional confirmado');
    $expect(str_contains($html, 'Estudante de Técnico Integrado em Informática no IFMS'), 'Apresentação preserva condição de estudante ativo');
    $expect(str_contains($html, 'Busco continuar evoluindo em desenvolvimento de software'), 'Objetivo profissional confirmado');
    $expect(str_contains($html, 'Nenhum projeto em destaque'), 'Estado vazio dos destaques sem projetos inventados');
    $expect(!str_contains($html, 'id="outros-projetos-titulo"'), 'Catálogo vazio não cria grupo secundário sem conteúdo');
    $expect(str_contains($html, 'Habilidades a confirmar.'), 'Habilidades pendentes explicitamente sinalizadas');
    $expect(!str_contains($html, 'class="skill-category"'), 'Estado vazio não atribui habilidades ao proprietário');
    $expect(str_contains($html, 'Contatos a confirmar.'), 'Contatos pendentes explicitamente sinalizados');
    $expect(!str_contains($html, 'mailto:') && !str_contains($html, ' download>'), 'Nenhum contato ou currículo fictício no estado vazio');
    $expect(str_contains($html, 'href="' . $root . '/projetos"'), 'Home permite acessar catálogo');
    $expect(substr_count($html, '<h1 ') === 1, 'Título principal único na home');
    $expect(str_contains($html, 'Desenvolvedor Web Full Stack'), 'Posicionamento profissional confirmado');
    foreach (['experiencia', 'sobre', 'projetos', 'habilidades', 'contato'] as $section) {
        $expect(str_contains($html, '<section id="' . $section . '"'), 'Seção disponível: ' . $section);
        $expect(str_contains($html, 'href="' . $root . '/#' . $section . '"'), 'Navegação para a seção: ' . $section);
    }
    preg_match('~<section class="home-hero"[^>]*>(.*?)</section>~s', $html, $heroMatch);
    $hero = $heroMatch[1] ?? '';
    $expect(str_contains($hero, '<p class="hero-subtitle">Desenvolvedor Web e estudante</p>'), 'Hero com subtítulo exato aprovado');
    $expect(!str_contains($hero, 'IFMS') && !str_contains($hero, 'Full Stack'), 'Hero não mistura subtítulo com formação ou título profissional');
    $expect(!str_contains($hero, 'class="button') && !str_contains($hero, '<button'), 'Hero sem botões CTA');
    $expect(str_contains($hero, 'src="' . $root . '/assets/images/meu-retrato.png"') && str_contains($hero, 'alt="Retrato de Heitor Silva Vareiro"') && str_contains($hero, 'width="1122" height="1402" decoding="async" fetchpriority="high"'), 'Retrato renomeado tem caminho-base, texto alternativo e dimensões reais');
    $expect(!str_contains($hero, 'class="social-links"'), 'Sem contatos, hero não cria links ou espaços vazios de redes');
    preg_match('~<section id="sobre"[^>]*>(.*?)</section>~s', $html, $aboutMatch);
    $profile = (new Profile())->all();
    foreach (array_merge([$profile['headline'], $profile['student_status'], $profile['intro']], $profile['about']) as $text) {
        $expect(str_contains($aboutMatch[1] ?? '', htmlspecialchars($text, ENT_QUOTES, 'UTF-8')), 'Texto profissional preservado em Sobre mim');
    }
    $expect(str_contains($aboutMatch[1] ?? '', 'id="objetivos"') && str_contains($aboutMatch[1] ?? '', htmlspecialchars($profile['goals'], ENT_QUOTES, 'UTF-8')), 'Objetivos preservados dentro de Sobre, com âncora antiga');
    $expect(!str_contains($aboutMatch[1] ?? '', 'id="formacao"') && !str_contains($aboutMatch[1] ?? '', 'certification-list'), 'Formação não é duplicada dentro de Sobre');
    preg_match('~<section id="formacao"[^>]*>(.*?)</section>~s', $html, $educationMatch);
    foreach ($profile['education'] as $text) {
        $expect(str_contains($educationMatch[1] ?? '', htmlspecialchars($text, ENT_QUOTES, 'UTF-8')), 'Dado de formação preservado na seção própria');
    }
    foreach ($profile['certifications'] as $text) {
        $expect(str_contains($educationMatch[1] ?? '', htmlspecialchars($text, ENT_QUOTES, 'UTF-8')), 'Nome de certificado preservado sem novos campos');
    }
    $expect(substr_count($educationMatch[1] ?? '', '<li>') === count($profile['certifications']), 'Certificações mantêm apenas os registros confirmados');
    $expect(strpos($html, 'id="formacao"') > strpos($html, 'id="habilidades"') && strpos($html, 'id="formacao"') < strpos($html, 'id="contato"'), 'Formação segue habilidades e antecede contato');
    preg_match_all('~<section\b[^>]*>~', $html, $sectionTags);
    $expect(str_contains($sectionTags[0][0] ?? '', 'class="home-hero"') && str_contains($sectionTags[0][1] ?? '', 'id="experiencia"') && str_contains($sectionTags[0][2] ?? '', 'id="sobre"'), 'Experiência é primeira seção após o hero, seguida de Sobre');
    preg_match('~<section id="experiencia"[^>]*>(.*?)</section>~s', $html, $experienceMatch);
    foreach ($profile['experience'] as $key => $value) {
        foreach (is_array($value) ? $value : [$value] as $text) {
            $expect(str_contains($experienceMatch[1] ?? '', htmlspecialchars($text, ENT_QUOTES, 'UTF-8')), 'Dado profissional preservado na experiência: ' . $key);
        }
    }
    $expect(!str_contains($aboutMatch[1] ?? '', 'id="estagio-titulo"'), 'Ficha de estágio não é duplicada em Sobre');
    $expect(str_contains($html, '&lt;script&gt;'), 'Escaping do título');
    $expect(!str_contains($html, '<script>'), 'Ausência de HTML arbitrário no título');
    $expectLayout($html, $root, $homeHtml, $emptyContact->all());
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
    $expect(str_contains($html, 'href="' . $root . '/#projetos"'), 'Catálogo permite retornar à seção de projetos da home');
    $expect(!str_contains($html, 'aria-current="page"'), 'Âncoras da home não marcam catálogo como página atual');
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
        $expect(!str_contains($html, 'Imagem em preparação') && !str_contains($html, 'project-image--placeholder'), 'Imagem ausente omitida sem placeholder');
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
            $expect(str_contains($detail, 'href="' . $root . '/#projetos">Voltar aos projetos</a>'), 'Retorno principal do detalhe vai à seção da home');
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
        (new HomeController($config, $model, $emptySkills, $emptyContact))->index();
        $html = ob_get_clean();
        $expect(substr_count($html, 'class="project-card"') === 2, 'Home apresenta destaques e secundários');
        preg_match('~<section class="project-group" aria-labelledby="destaques-titulo">(.*?)</section>~s', $html, $featuredMatch);
        preg_match('~<section class="project-group" aria-labelledby="outros-projetos-titulo">(.*?)</section>~s', $html, $otherMatch);
        $expect(substr_count($featuredMatch[1] ?? '', 'class="project-card"') === 1 && !str_contains($featuredMatch[1] ?? '', 'Teste sem opcionais'), 'Grupo amplo contém somente destaque');
        $expect(substr_count($otherMatch[1] ?? '', 'class="project-card"') === 1 && str_contains($otherMatch[1] ?? '', 'Teste sem opcionais'), 'Item não destacado aparece na lista secundária');
        $expect(str_contains($featuredMatch[1] ?? '', 'project-list--featured') && str_contains($otherMatch[1] ?? '', 'project-list--compact'), 'Grupos usam apresentações diferentes');
        $expect(substr_count($html, 'href="' . $root . '/projetos/teste-destaque"') === 1 && substr_count($html, 'href="' . $root . '/projetos/teste-sem-opcionais"') === 1, 'Projeto aparece uma vez na home sem duplicação entre grupos');
        $expect(str_contains($featuredMatch[1] ?? '', 'data-project-image') && !str_contains($otherMatch[1] ?? '', '<img'), 'Imagem real habilita mídia do destaque; secundário continua compacto');
        $expect(!str_contains($html, '<script>') && !str_contains($html, '<img src=x'), 'As duas apresentações escapam conteúdo dinâmico');
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
        (new HomeController(['name' => 'Teste', 'base_path' => ''], $model, $emptySkills, $emptyContact))->index();
        $html = ob_get_clean();
        $expect(str_contains($html, 'Nenhum projeto em destaque'), 'Home trata catálogo preenchido sem featured');
        $expect(str_contains($html, 'project-list--compact') && !str_contains($html, 'data-project-image'), 'Sem destaques, demais projetos continuam na lista e sem áreas de imagem inválida');
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
        (new HomeController($config, $emptyProjects, $skills, $emptyContact))->index();
        $html = ob_get_clean();
        $expect(http_response_code() === 200, 'Home com habilidades responde 200');
        $expect(substr_count($html, 'class="skill-category"') === 2, 'Home apresenta cada categoria preenchida');
        preg_match('~<section id="habilidades".*?</section>~s', $html, $skillsSection);
        $expect(substr_count($skillsSection[0], 'class="skill-list"') === 2, 'Habilidades usam listas por categoria');
        $expect(str_contains($html, '<h3>Linguagens de teste</h3>'), 'Categoria usa heading abaixo do título da seção');
        $expect(str_contains($html, '<span class="skill-name">PHP</span>') && str_contains($html, '<span class="skill-name">JavaScript</span>'), 'Itens vêm do arquivo sem editar layout e mantêm nomes legíveis');
        $expect(str_contains($html, 'Ferramentas &lt;script&gt;alert(&quot;x&quot;)&lt;/script&gt;'), 'Nome da categoria escapado');
        $expect(str_contains($html, '<span class="skill-name">Git &amp; revisão</span>') && str_contains($html, '&lt;img src=x onerror=alert(1)&gt;'), 'Habilidades escapadas como texto');
        $itemCount = array_sum(array_map(static fn (array $group): int => count($group['items']), $skills->all()));
        $expect(substr_count($skillsSection[0], 'class="skill-icon"') === $itemCount && substr_count($skillsSection[0], 'class="skill-name"') === $itemCount, 'Cada habilidade tem ícone decorativo e nome independente');
        $withoutIcons = preg_replace('~<svg\b.*?</svg>~s', '', $skillsSection[0]);
        $expect(str_contains($withoutIcons, '>PHP</span>') && str_contains($withoutIcons, '>Git &amp; revisão</span>'), 'Nomes continuam disponíveis sem os ícones');
        $expect(!str_contains($html, '<script>') && !str_contains($html, '<img src=x'), 'Dados de habilidades não injetam HTML');
        $expect(!str_contains($html, 'Habilidades a confirmar.') && !str_contains($html, 'Categoria vazia'), 'Sem aviso pendente ou categoria vazia quando há habilidades');
        $expect(str_contains($html, 'id="habilidades" class="home-section" aria-labelledby="habilidades-titulo" tabindex="-1"'), 'Seção tem heading associado e destino focável por fragmento');
        $expect(str_contains($html, 'href="' . $root . '/#habilidades"'), 'Menu acessa habilidades com caminho-base');
        $expect(strpos($html, 'id="habilidades"') > strpos($html, 'id="projetos"'), 'Seção de habilidades segue os projetos');
        preg_match_all('/\bid="([^"]+)"/', $html, $ids);
        preg_match_all('/aria-labelledby="([^"]+)"/', $html, $labels);
        $expect(array_diff($labels[1], $ids[1]) === [], 'Headings referenciados existem na home com habilidades');
        $expectLayout($html, $root, $html, $emptyContact->all());

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
    (new HomeController(['name' => 'Teste', 'base_path' => ''], $emptyProjects, new Skill($skillFile), $emptyContact))->index();
    $html = ob_get_clean();
    $expect(str_contains($html, '<h3>Nova categoria</h3>') && str_contains($html, '<span class="skill-name">Nova habilidade de teste</span>'), 'Atualizar apenas o arquivo inclui categoria e habilidade na home');
} finally {
    unlink($skillFile);
}

// Contatos fictícios isolados: não alteram a configuração real nem enviam e-mail.
$contactFile = tempnam(sys_get_temp_dir(), 'portfolio-contact-');
$resumeFile = tempnam(dirname(__DIR__) . '/public/assets/documents', 'test-resume-');
rename($resumeFile, $resumeFile . '.pdf');
$resumeFile .= '.pdf';
$resumePath = 'assets/documents/' . basename($resumeFile);
// PDF mínimo de uma página em branco, para verificar a disponibilidade do arquivo.
$pdf = "%PDF-1.4\n";
$offsets = [0];
foreach (['<< /Type /Catalog /Pages 2 0 R >>', '<< /Type /Pages /Kids [3 0 R] /Count 1 >>', '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 100 100] >>'] as $object) {
    $offsets[] = strlen($pdf);
    $pdf .= (count($offsets) - 1) . " 0 obj\n" . $object . "\nendobj\n";
}
$xref = strlen($pdf);
$pdf .= "xref\n0 4\n0000000000 65535 f \n";
foreach (array_slice($offsets, 1) as $offset) {
    $pdf .= sprintf('%010d 00000 n ', $offset) . "\n";
}
$pdf .= "trailer\n<< /Size 4 /Root 1 0 R >>\nstartxref\n" . $xref . "\n%%EOF\n";
file_put_contents($resumeFile, $pdf);

try {
    $writeContact = static function (array $data) use ($contactFile): void {
        file_put_contents($contactFile, '<?php return ' . var_export($data, true) . ';');
    };
    $expectedEmpty = ['github_url' => null, 'linkedin_url' => null, 'email' => null, 'resume' => null];
    $expect($emptyContact->all() === $expectedEmpty, 'Campos de contato omitidos assumem null');
    $writeContact($expectedEmpty);
    $expect((new Contact($contactFile))->all() === $expectedEmpty, 'Contatos explicitamente nulos permanecem ausentes');
    $fixture = [
        'github_url' => 'https://github.com/exemplo-de-teste?tab=repositories&sort=updated',
        'linkedin_url' => 'https://www.linkedin.com/in/exemplo-de-teste/',
        'email' => ' teste+portfolio@example.org ',
        'resume' => $resumePath,
    ];
    $writeContact($fixture);
    $contact = new Contact($contactFile);
    $expect($contact->all()['email'] === 'teste+portfolio@example.org', 'E-mail validado e sem espaços nas extremidades');
    $expect($contact->all()['resume'] === $resumePath, 'PDF local existente habilitado');

    foreach (['', '/portifolio', '/outro/caminho/'] as $basePath) {
        $root = rtrim($basePath, '/');
        $config = ['name' => 'Teste', 'base_path' => $basePath];
        http_response_code(200);
        ob_start();
        (new HomeController($config, $emptyProjects, $emptySkills, $contact))->index();
        $html = ob_get_clean();
        $expect(http_response_code() === 200, 'Home com contatos responde 200');
        $expect(str_contains($html, 'id="contato" class="home-section" aria-labelledby="contato-titulo" tabindex="-1"'), 'Contato tem heading e destino focável por fragmento');
        $expect(str_contains($html, 'href="' . $root . '/#contato"'), 'Menu aponta para contato com caminho-base');
        $expect(str_contains($html, 'href="https://github.com/exemplo-de-teste?tab=repositories&amp;sort=updated"'), 'GitHub validado e atributo escapado');
        $expect(str_contains($html, 'href="https://www.linkedin.com/in/exemplo-de-teste/"'), 'LinkedIn disponível');
        $expect(str_contains($html, 'href="mailto:teste%2Bportfolio@example.org"'), 'Mailto codifica caracteres especiais do endereço');
        $expect(str_contains($html, '>E-mail de contato</a>'), 'Link de e-mail tem rótulo acessível aprovado');
        $expect(str_contains($html, 'href="' . $root . '/' . $resumePath . '" download>Baixar currículo (PDF)'), 'Download do PDF respeita caminho-base');
        $expect(!str_contains($html, 'Contatos a confirmar.'), 'Aviso pendente omitido com conteúdo disponível');
        preg_match('~<section class="home-hero"[^>]*>(.*?)</section>~s', $html, $heroMatch);
        $hero = $heroMatch[1] ?? '';
        $expect(substr_count($hero, '<a ') === 2, 'Hero exibe somente as duas redes aprovadas');
        $expect(str_contains($hero, 'aria-label="Perfil no GitHub"') && str_contains($hero, 'aria-label="Perfil no LinkedIn"'), 'Ícones sociais têm nomes acessíveis');
        $expect(str_contains($hero, htmlspecialchars($fixture['github_url'], ENT_QUOTES, 'UTF-8')) && str_contains($hero, htmlspecialchars($fixture['linkedin_url'], ENT_QUOTES, 'UTF-8')), 'Hero reutiliza contatos injetados e escapados');
        $expect(!str_contains($hero, 'mailto:') && !str_contains($hero, 'download') && !str_contains($hero, 'class="button'), 'E-mail, PDF e CTA não entram no hero');
        $expectFooter($html, $contact->all());
        $expect(!str_contains($html, 'https://github.com/heitorsv001'), 'Contatos injetados na home não são substituídos pela configuração padrão');
        $expect(!str_contains($html, 'target=') && !str_contains($html, '<form'), 'Links na mesma aba e sem formulário de contato');
        $expect(strpos($html, 'id="contato"') > strpos($html, 'id="habilidades"'), 'Contato segue habilidades');
        preg_match_all('/\bid="([^"]+)"/', $html, $ids);
        preg_match_all('/aria-labelledby="([^"]+)"/', $html, $labels);
        $expect(count($ids[1]) === count(array_unique($ids[1])) && array_diff($labels[1], $ids[1]) === [], 'IDs e headings válidos na home com contatos');

        foreach (['catalog', 'detail', 'error', 'unknown-slug', 'hidden-slug'] as $page) {
            ob_start();
            if ($page === 'catalog') {
                (new ProjectController($config, $emptyProjects))->index();
            } elseif ($page === 'detail') {
                (new ProjectController($config))->show('gerenciador-de-permanencias');
            } elseif ($page === 'unknown-slug') {
                (new ProjectController($config, $emptyProjects))->show('inexistente');
            } elseif ($page === 'hidden-slug') {
                (new ProjectController($config))->show('portfolio-mvc-php');
            } else {
                (new ErrorController($config))->notFound();
            }
            $otherHtml = ob_get_clean();
            $expect(str_contains($otherHtml, 'href="' . $root . '/#contato"'), 'Retorno à seção de contato a partir de ' . $page);
            $expectFooter($otherHtml, (new Contact())->all());
        }
    }

    foreach ([$expectedEmpty, ['github_url' => $fixture['github_url']], ['linkedin_url' => $fixture['linkedin_url']]] as $partialContact) {
        $writeContact($partialContact);
        $model = new Contact($contactFile);
        ob_start();
        (new HomeController(['name' => 'Teste', 'base_path' => ''], $emptyProjects, $emptySkills, $model))->index();
        $html = ob_get_clean();
        $expectFooter($html, $model->all());
        preg_match('~<section class="home-hero"[^>]*>(.*?)</section>~s', $html, $heroMatch);
        $expect(substr_count($heroMatch[1] ?? '', '<a ') === count(array_filter([$model->all()['github_url'], $model->all()['linkedin_url']])), 'Hero e rodapé respeitam os mesmos contatos parciais ou ausentes');
    }

    foreach (['', 'endereço inválido', '//github.com/teste', 'javascript:alert(1)', 'data:text/html,teste', 'ftp://example.org', 'https://user:pass@example.org', 'https://example.org/" onclick="alert(1)', "https://example.org/\r\n"] as $url) {
        $writeContact(['github_url' => $url, 'linkedin_url' => $url]);
        $data = (new Contact($contactFile))->all();
        $expect($data['github_url'] === null && $data['linkedin_url'] === null, 'URLs inválidas ou inseguras omitidas: ' . json_encode($url));
    }
    foreach (['', 'email-invalido', 'mailto:teste@example.org', 'teste@example.org?subject=teste', "teste@example.org\r\nBcc:outro@example.org", '<script>@example.org'] as $email) {
        $writeContact(['email' => $email]);
        $expect((new Contact($contactFile))->all()['email'] === null, 'E-mail inválido ou com parâmetros omitido: ' . json_encode($email));
    }
    $writeContact(['email' => 'teste?&%0A@example.org']);
    ob_start();
    (new HomeController(['name' => 'Teste', 'base_path' => ''], $emptyProjects, $emptySkills, new Contact($contactFile)))->index();
    $html = ob_get_clean();
    $expect(str_contains($html, 'href="mailto:teste%3F%26%250A@example.org"'), 'Caracteres especiais no local-part não viram parâmetros ou cabeçalhos de mailto');
    $expect(!str_contains($html, 'Perfil no GitHub') && !str_contains($html, ' download>'), 'Contato parcial não cria links ausentes');

    foreach (['assets/documents/inexistente.pdf', '../config/app.php', 'assets/documents/../../config/app.php', 'https://example.org/curriculo.pdf', '/assets/documents/curriculo.pdf', 'assets/documents/curriculo.php', 'assets/documents/curriculo.pdf?download=1'] as $path) {
        $writeContact(['resume' => $path]);
        $expect((new Contact($contactFile))->all()['resume'] === null, 'Currículo ausente ou fora do padrão omitido: ' . $path);
    }
    $writeContact(['resume' => $resumePath]);
    ob_start();
    (new HomeController(['name' => 'Teste', 'base_path' => ''], $emptyProjects, $emptySkills, new Contact($contactFile)))->index();
    $html = ob_get_clean();
    preg_match('~<ul class="contact-links"[^>]*>(.*?)</ul>~s', $html, $resumeLinks);
    $expect(substr_count($resumeLinks[1] ?? '', '<li>') === 1
        && str_contains($resumeLinks[1] ?? '', 'href="/' . $resumePath . '" download>Baixar currículo (PDF)'),
        'Currículo isolado mantém um único download na lista de contatos');

    unlink($resumeFile);
    clearstatcache();
    $expect((new Contact($contactFile))->all()['resume'] === null, 'Remoção do arquivo desabilita download');
    $writeContact(['github_url' => 'javascript:alert(1)', 'email' => 'inválido', 'resume' => $resumePath]);
    ob_start();
    (new HomeController(['name' => 'Teste', 'base_path' => ''], $emptyProjects, $emptySkills, new Contact($contactFile)))->index();
    $html = ob_get_clean();
    $expect(str_contains($html, 'Contatos a confirmar.') && !str_contains($html, 'javascript:') && !str_contains($html, ' download>'), 'Valores inválidos usam estado vazio sem links quebrados');
} finally {
    unlink($contactFile);
    if (is_file($resumeFile)) {
        unlink($resumeFile);
    }
}

$home = new HomeController(['name' => 'Teste', 'base_path' => '/teste" onclick="alert(1)'], $emptyProjects, $emptySkills, $emptyContact);
ob_start();
$home->index();
$html = ob_get_clean();
$expect(str_contains($html, 'href="/teste&quot; onclick=&quot;alert(1)/assets/css/style.css?v='), 'Escaping do atributo de stylesheet');
$expect(str_contains($html, 'href="/teste&quot; onclick=&quot;alert(1)/"'), 'Escaping do caminho-base na navegação');
$expect(str_contains($html, 'src="/teste&quot; onclick=&quot;alert(1)/assets/js/theme-init.js?v='), 'Escaping do caminho-base no script de tema');
$expect(str_contains($html, 'src="/teste&quot; onclick=&quot;alert(1)/assets/js/site.js?v='), 'Escaping do caminho-base no script de interações');
$expect(str_contains($html, 'src="/teste&quot; onclick=&quot;alert(1)/assets/js/hero-particles.js?v='), 'Escaping do caminho-base no script de partículas');

// SEO e imagens: fixtures isoladas, sem domínio ou projetos reais inventados.
$seoFile = tempnam(sys_get_temp_dir(), 'portfolio-seo-');
$seoImage = tempnam(dirname(__DIR__) . '/public/assets/images', 'test-seo-');
rename($seoImage, $seoImage . '.png');
$seoImage .= '.png';
$seoImagePath = 'assets/images/' . basename($seoImage);
file_put_contents($seoImage, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+aBLsAAAAASUVORK5CYII='));
$meta = static function (string $html, string $key): ?string {
    if (!preg_match('/<meta (?:name|property)="' . preg_quote($key, '/') . '" content="([^"]*)">/', $html, $matches)) {
        return null;
    }
    return html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
};
try {
    $seoFixture = [[
        'id' => 1, 'slug' => 'projeto-seo', 'title' => 'Projeto "especial" & <script>teste</script>',
        'summary' => 'Resumo "com aspas" & <script>alert(1)</script>',
        'description' => 'Descrição de teste.', 'image' => $seoImagePath, 'featured' => true,
    ]];
    file_put_contents($seoFile, '<?php return ' . var_export($seoFixture, true) . ';');
    $seoProjects = new Project($seoFile);
    $expect($seoProjects->all()[0]['image_width'] === 1 && $seoProjects->all()[0]['image_height'] === 1, 'Model fornece dimensões reais da imagem');
    $defaultImage = getimagesize(dirname(__DIR__) . '/public/assets/images/social-preview.png');
    $expect($defaultImage[0] === 1200 && $defaultImage[1] === 630 && $defaultImage['mime'] === 'image/png', 'Imagem padrão de compartilhamento PNG válida');

    foreach (['', '/portifolio', '/outro/caminho/'] as $basePath) {
        $root = rtrim($basePath, '/');
        $config = ['name' => 'Teste "SEO" & <script>nome</script>', 'base_path' => $basePath, 'origin' => 'https://portfolio.example/'];
        $catalog = new ProjectController($config, $seoProjects);
        $router = new Router($basePath);
        $router->get('/projetos/{slug}', [$catalog, 'show']);

        foreach (['home' => '/', 'catalog' => '/projetos', 'detail' => '/projetos/projeto-seo'] as $page => $path) {
            http_response_code(200);
            ob_start();
            if ($page === 'home') {
                (new HomeController($config, $emptyProjects, $emptySkills, $emptyContact))->index();
            } elseif ($page === 'catalog') {
                $catalog->index();
            } else {
                $router->dispatch('GET', $root . '/projetos/projeto%2Dseo/?origem=teste');
            }
            $html = ob_get_clean();
            $canonical = 'https://portfolio.example' . $root . $path;
            $expect(str_contains($html, '<link rel="canonical" href="' . $canonical . '">'), 'Canonical absoluto usa rota conhecida e remove query/codificação: ' . $page);
            $expect($meta($html, 'og:url') === $canonical, 'Open Graph e canonical coincidem: ' . $page);
            $expect($meta($html, 'og:site_name') === $config['name'], 'Nome do site escapado nos metadados');
            $expect($meta($html, 'og:type') === 'website' && $meta($html, 'og:locale') === 'pt_BR', 'Tipo e locale de compartilhamento');
            $expect($meta($html, 'description') !== null && $meta($html, 'description') === $meta($html, 'og:description'), 'Descrição única consistente com Open Graph');
            $expect(substr_count($html, '<meta name="description"') === 1 && substr_count($html, '<title>') === 1, 'Metadados de título/descrição sem duplicação');
            $expect($meta($html, 'robots') === null, 'Página válida não recebe noindex');
            $expect(!str_contains($html, '<script>') && !str_contains($html, 'content="Resumo "'), 'Conteúdo de metadados não injeta HTML ou atributos');
            if ($page === 'detail') {
                $expect($meta($html, 'description') === $seoFixture[0]['summary'], 'Detalhe usa resumo do projeto como descrição');
                $expect($meta($html, 'og:title') === $seoFixture[0]['title'] . ' — ' . $config['name'], 'Título específico do detalhe em Open Graph');
                $expect($meta($html, 'og:image') === 'https://portfolio.example' . $root . '/' . $seoImagePath, 'Detalhe compartilha imagem do projeto');
                $expect($meta($html, 'og:image:width') === '1' && $meta($html, 'og:image:height') === '1', 'Open Graph usa dimensões intrínsecas');
                $expect($meta($html, 'og:image:alt') === 'Prévia do projeto ' . $seoFixture[0]['title'], 'Alt de compartilhamento específico e escapado');
                $expect(str_contains($html, 'width="1" height="1" decoding="async"'), 'Imagem de detalhe reserva proporção real');
            } else {
                $expect($meta($html, 'og:image') === 'https://portfolio.example' . $root . '/assets/images/social-preview.png', 'Home/catálogo compartilham imagem padrão');
                $expect($meta($html, 'og:image:width') === '1200' && $meta($html, 'og:image:height') === '630', 'Dimensões padrão de compartilhamento');
                if ($page === 'catalog') {
                    $expect(str_contains($html, 'loading="lazy" decoding="async" width="1" height="1"'), 'Card usa dimensões reais e lazy loading');
                }
            }
        }

        ob_start();
        $catalog->show('inexistente');
        $html = ob_get_clean();
        $expect(http_response_code() === 404 && $meta($html, 'robots') === 'noindex, follow', 'Erro 404 não é indexável');
        $expect(!str_contains($html, 'rel="canonical"') && $meta($html, 'og:url') === null && $meta($html, 'og:title') === null, '404 não anuncia URL canônica ou compartilhamento de página válida');
    }

    foreach ([null, '', '//portfolio.example', 'javascript:alert(1)', 'ftp://portfolio.example', 'https://user:pass@portfolio.example', 'https://portfolio.example/subpasta', 'https://portfolio.example?teste=1', 'https://portfolio.example/#fragmento', 'https://portfolio.example/" onclick="alert(1)'] as $origin) {
        ob_start();
        (new HomeController(['name' => 'Teste', 'base_path' => '', 'origin' => $origin], $emptyProjects, $emptySkills, $emptyContact))->index();
        $html = ob_get_clean();
        $expect(!str_contains($html, 'rel="canonical"') && $meta($html, 'og:url') === null && $meta($html, 'og:image') === null, 'Origem ausente/inválida não gera URLs absolutas: ' . json_encode($origin));
        $expect($meta($html, 'og:title') !== null && $meta($html, 'description') !== null, 'Metadados textuais disponíveis sem domínio');
    }

    $seoFixture[0]['image'] = null;
    file_put_contents($seoFile, '<?php return ' . var_export($seoFixture, true) . ';');
    ob_start();
    (new ProjectController(['name' => 'Teste', 'base_path' => '', 'origin' => 'https://portfolio.example'], new Project($seoFile)))->show('projeto-seo');
    $html = ob_get_clean();
    $expect($meta($html, 'og:image') === 'https://portfolio.example/assets/images/social-preview.png', 'Projeto sem imagem própria usa preview padrão');

    $seoFixture[0]['image'] = $seoImagePath;
    file_put_contents($seoImage, 'Arquivo que não é uma imagem.');
    file_put_contents($seoFile, '<?php return ' . var_export($seoFixture, true) . ';');
    $project = (new Project($seoFile))->all()[0];
    $expect($project['image'] === null && $project['image_width'] === null && $project['image_height'] === null, 'Arquivo de imagem ilegível usa fallback sem dimensões inventadas');
} finally {
    unlink($seoFile);
    unlink($seoImage);
}

// Contrastes dos tokens usados em texto, botões e foco; não substitui auditoria visual.
$css = file_get_contents(dirname(__DIR__) . '/public/assets/css/style.css');
$expect(str_contains($css, ':root,' . "\n" . '[data-theme="dark"]'), 'Tema escuro como fallback sem JavaScript');
foreach (['dark', 'light'] as $theme) {
    preg_match('/\[data-theme="' . $theme . '"\]\s*\{([^}]+)\}/', $css, $themeBlock);
    $expect(isset($themeBlock[1]), 'Tokens definidos para o tema ' . $theme);
    preg_match_all('/--color-([a-z-]+):\s*#([0-9a-f]{6});/i', $themeBlock[1] ?? '', $colorMatches, PREG_SET_ORDER);
    $luminance = [];
    foreach ($colorMatches as $color) {
        $rgb = array_map(static function (string $channel): float {
            $value = hexdec($channel) / 255;
            return $value <= 0.04045 ? $value / 12.92 : (($value + 0.055) / 1.055) ** 2.4;
        }, str_split($color[2], 2));
        $luminance[$color[1]] = 0.2126 * $rgb[0] + 0.7152 * $rgb[1] + 0.0722 * $rgb[2];
    }
    foreach (['background', 'surface', 'surface-hover'] as $background) {
        foreach (['text', 'muted', 'accent', 'accent-hover', 'focus'] as $foreground) {
            $ratio = (max($luminance[$foreground], $luminance[$background]) + 0.05)
                / (min($luminance[$foreground], $luminance[$background]) + 0.05);
            $expect($ratio >= ($foreground === 'focus' ? 3 : 4.5), 'Contraste em ' . $theme . ': ' . $foreground . ' sobre ' . $background);
        }
    }
    foreach (['accent', 'accent-hover'] as $background) {
        $ratio = (max($luminance['on-accent'], $luminance[$background]) + 0.05)
            / (min($luminance['on-accent'], $luminance[$background]) + 0.05);
        $expect($ratio >= 4.5, 'Texto do botão em ' . $theme . ' sobre ' . $background);
    }
}

$previousOrigin = getenv('APP_ORIGIN');
putenv('APP_ORIGIN');
$config = require dirname(__DIR__) . '/config/app.php';
$expect($config['origin'] === null, 'Domínio não é inferido na ausência de APP_ORIGIN');
putenv('APP_ORIGIN=https://portfolio.example');
$config = require dirname(__DIR__) . '/config/app.php';
$expect($config['origin'] === 'https://portfolio.example', 'Origem pública configurável por ambiente');
if ($previousOrigin === false) {
    putenv('APP_ORIGIN');
} else {
    putenv('APP_ORIGIN=' . $previousOrigin);
}

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

$visibilityFile = tempnam(sys_get_temp_dir(), 'portfolio-visibility-');
$profileFile = tempnam(sys_get_temp_dir(), 'portfolio-profile-');
try {
    $hidden = [
        'id' => 1,
        'slug' => 'projeto-oculto',
        'title' => 'Projeto oculto',
        'summary' => 'Resumo privado de teste',
        'description' => 'Descrição privada de teste',
        'featured' => true,
        'visible' => false,
    ];
    file_put_contents($visibilityFile, '<?php return ' . var_export([$hidden], true) . ';');
    $projects = new Project($visibilityFile);
    $expect($projects->all() === [] && $projects->featured() === [], 'Projeto oculto não vaza no catálogo ou destaques mesmo com featured ativo');
    $expect($projects->findBySlug('projeto-oculto') === null, 'Projeto oculto não pode ser consultado por slug');
    ob_start();
    (new ProjectController(['name' => 'Teste', 'base_path' => ''], $projects))->show('projeto-oculto');
    $html = ob_get_clean();
    $expect(http_response_code() === 404 && !str_contains($html, $hidden['summary']), 'URL direta de projeto oculto retorna 404 sem expor dados');
    ob_start();
    (new HomeController(['name' => 'Teste', 'base_path' => ''], $projects, $emptySkills, $emptyContact))->index();
    $html = ob_get_clean();
    $expect(!str_contains($html, $hidden['summary']) && !str_contains($html, $hidden['title']), 'Projeto oculto não aparece em nenhum grupo da home');

    $hidden['visible'] = true;
    $hidden['status'] = 'Estado <script>alert("x")</script>';
    file_put_contents($visibilityFile, '<?php return ' . var_export([$hidden], true) . ';');
    $projects = new Project($visibilityFile);
    $expect(count($projects->all()) === 1 && count($projects->featured()) === 1, 'Visibilidade pode ser habilitada pelo arquivo de dados');
    foreach (['catalog', 'detail'] as $page) {
        ob_start();
        $controller = new ProjectController(['name' => 'Teste', 'base_path' => ''], $projects);
        if ($page === 'catalog') {
            $controller->index();
        } else {
            $controller->show('projeto-oculto');
        }
        $html = ob_get_clean();
        $expect(str_contains($html, 'Estado &lt;script&gt;alert(&quot;x&quot;)&lt;/script&gt;') && !str_contains($html, '<script>'), 'Ressalva editorial escapada em ' . $page);
    }

    $profile = (new Profile())->all();
    $escapeProfile = static function (array $values) use (&$escapeProfile): array {
        foreach ($values as &$value) {
            $value = is_array($value) ? $escapeProfile($value) : '<script>alert("perfil")</script>';
        }
        unset($value);
        return $values;
    };
    file_put_contents($profileFile, '<?php return ' . var_export($escapeProfile($profile), true) . ';');
    http_response_code(200);
    ob_start();
    (new HomeController(
        ['name' => 'Teste', 'base_path' => ''],
        $emptyProjects,
        $emptySkills,
        $emptyContact,
        new Profile($profileFile)
    ))->index();
    $html = ob_get_clean();
    $expect(!str_contains($html, '<script>') && str_contains($html, '&lt;script&gt;alert(&quot;perfil&quot;)&lt;/script&gt;'), 'Dados de perfil, formação, experiência e certificados escapados na home e metadados');
    $expect(str_contains($html, '<li>&lt;script&gt;alert(&quot;perfil&quot;)&lt;/script&gt;</li>'), 'Novas tags de tecnologia do estágio são escapadas');
} finally {
    unlink($visibilityFile);
    unlink($profileFile);
}

foreach ($failures as $failure) {
    fwrite(STDERR, 'FALHOU: ' . $failure . PHP_EOL);
}
echo ($checks - count($failures)) . '/' . $checks . ' verificações aprovadas.' . PHP_EOL;
exit($failures === [] ? 0 : 1);
