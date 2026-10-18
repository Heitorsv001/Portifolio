<?php
/** @var list<array<string, mixed>> $projects */
/** @var string $projectsUrl */
?>
<section class="home-hero" aria-labelledby="apresentacao-titulo">
    <p class="eyebrow">Portfólio em construção</p>
    <p class="hero-name">[Nome de exibição a informar]</p>
    <h1 id="apresentacao-titulo">Desenvolvedor <span class="hero-highlight">Full Stack</span></h1>
    <p class="home-intro">Este é meu portfólio pessoal. Minha apresentação e trajetória serão adicionadas após a confirmação das informações.</p>
    <div class="hero-actions">
        <a class="button" href="#projetos">Ir para projetos <span aria-hidden="true">&darr;</span></a>
        <a class="button button--secondary" href="#sobre">Sobre mim</a>
    </div>
</section>

<section id="sobre" class="home-section" aria-labelledby="sobre-titulo" tabindex="-1">
    <div class="section-heading">
        <p class="eyebrow">01 / Apresentação</p>
        <h2 id="sobre-titulo">Sobre mim</h2>
    </div>
    <div class="section-content">
        <p>Biografia em preparação. Este espaço apresentará minha trajetória pessoal e profissional com informações confirmadas.</p>
        <dl class="profile-details">
            <div>
                <dt>Formação</dt>
                <dd>A informar.</dd>
            </div>
            <div>
                <dt>Interesses</dt>
                <dd>A informar.</dd>
            </div>
        </dl>
    </div>
</section>

<section id="objetivos" class="home-section" aria-labelledby="objetivos-titulo" tabindex="-1">
    <div class="section-heading">
        <p class="eyebrow">02 / Próximos passos</p>
        <h2 id="objetivos-titulo">Objetivos profissionais</h2>
    </div>
    <div class="section-content">
        <p>Objetivos profissionais a informar. As áreas de interesse e os próximos passos da minha carreira serão apresentados aqui.</p>
    </div>
</section>

<section id="projetos" class="home-section" aria-labelledby="projetos-titulo" tabindex="-1">
    <div class="section-heading">
        <p class="eyebrow">03 / Projetos</p>
        <h2 id="projetos-titulo">Projetos em destaque</h2>
    </div>
    <div class="section-content">
        <?php if ($projects === []): ?>
            <div class="project-notice">
                <h3>Nenhum projeto em destaque</h3>
                <p>Os destaques serão apresentados após a confirmação dos projetos e a seleção dos itens pelo proprietário.</p>
            </div>
        <?php else: ?>
            <?php require dirname(__DIR__) . '/partials/project-list.php'; ?>
        <?php endif; ?>
        <a class="button button--secondary" href="<?= htmlspecialchars($projectsUrl, ENT_QUOTES, 'UTF-8') ?>">Ver todos os projetos <span aria-hidden="true">&rarr;</span></a>
    </div>
</section>

<?php require dirname(__DIR__) . '/partials/skills.php'; ?>
