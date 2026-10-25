<?php
/** @var list<array<string, mixed>> $projects */
/** @var list<array<string, mixed>> $otherProjects */
/** @var string $projectsUrl */
/** @var string $basePath */
/** @var array<string, mixed> $profile */
?>
<section class="home-hero" aria-labelledby="apresentacao-titulo">
    <canvas class="hero-particles" data-hero-particles aria-hidden="true"></canvas>
    <div class="hero-content container">
        <div class="hero-avatar">
            <img src="<?= htmlspecialchars($basePath . '/assets/images/meu-retrato.png', ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars('Retrato de ' . $profile['name'], ENT_QUOTES, 'UTF-8') ?>" width="1122" height="1402" decoding="async" fetchpriority="high">
        </div>
        <h1 id="apresentacao-titulo"><?= htmlspecialchars($profile['name'], ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="hero-subtitle">Desenvolvedor Web e estudante</p>
        <?php require dirname(__DIR__) . '/partials/social-links.php'; ?>
    </div>
</section>

<div class="container">
    <section id="experiencia" class="home-section" aria-labelledby="experiencia-titulo" tabindex="-1">
        <div class="section-heading">
            <p class="eyebrow">01 / Trajetória</p>
            <h2 id="experiencia-titulo">Experiência profissional</h2>
        </div>
        <div class="section-content">
            <article class="experience-item" aria-labelledby="estagio-titulo">
                <p class="experience-period"><?= htmlspecialchars($profile['experience']['period'], ENT_QUOTES, 'UTF-8') ?></p>
                <div class="experience-content">
                    <h3 id="estagio-titulo"><?= htmlspecialchars($profile['experience']['role'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <p class="experience-institution"><?= htmlspecialchars($profile['experience']['institution'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p class="experience-project"><?= htmlspecialchars($profile['experience']['project'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p><?= htmlspecialchars($profile['experience']['description'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php if (($profile['experience']['technologies'] ?? []) !== []): ?>
                        <ul class="experience-technologies" role="list" aria-label="Tecnologias utilizadas no estágio">
                            <?php foreach ($profile['experience']['technologies'] as $technology): ?>
                                <li><?= htmlspecialchars($technology, ENT_QUOTES, 'UTF-8') ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </article>
        </div>
    </section>

    <section id="sobre" class="home-section" aria-labelledby="sobre-titulo" tabindex="-1">
        <div class="section-heading">
            <p class="eyebrow">02 / Apresentação</p>
            <h2 id="sobre-titulo">Sobre mim</h2>
        </div>
        <div class="section-content">
            <p class="profile-headline"><?= htmlspecialchars($profile['headline'], ENT_QUOTES, 'UTF-8') ?></p>
            <p><?= htmlspecialchars($profile['student_status'], ENT_QUOTES, 'UTF-8') ?></p>
            <p><?= htmlspecialchars($profile['intro'], ENT_QUOTES, 'UTF-8') ?></p>
            <?php foreach ($profile['about'] as $paragraph): ?>
                <p><?= htmlspecialchars($paragraph, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endforeach; ?>
            <div id="objetivos" class="profile-goals" tabindex="-1">
                <h3 id="objetivos-titulo">Objetivos profissionais</h3>
                <p><?= htmlspecialchars($profile['goals'], ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        </div>
    </section>

    <section id="projetos" class="home-section home-section--projects" aria-labelledby="projetos-titulo" tabindex="-1">
        <div class="section-heading">
            <p class="eyebrow">03 / Projetos</p>
            <h2 id="projetos-titulo">Projetos</h2>
        </div>
        <div class="section-content">
            <section class="project-group" aria-labelledby="destaques-titulo">
                <h3 id="destaques-titulo">Em destaque</h3>
                <?php if ($projects === []): ?>
                    <div class="project-notice">
                        <h4>Nenhum projeto em destaque</h4>
                        <p>Os destaques serão apresentados após a confirmação dos projetos e a seleção dos itens pelo proprietário.</p>
                    </div>
                <?php else: ?>
                    <?php
                    $projectItems = $projects;
                    $projectLayout = 'featured';
                    require dirname(__DIR__) . '/partials/project-list.php';
                    ?>
                <?php endif; ?>
            </section>
            <?php if ($otherProjects !== []): ?>
                <section class="project-group" aria-labelledby="outros-projetos-titulo">
                    <h3 id="outros-projetos-titulo">Outros projetos</h3>
                    <?php
                    $projectItems = $otherProjects;
                    $projectLayout = 'compact';
                    require dirname(__DIR__) . '/partials/project-list.php';
                    ?>
                </section>
            <?php endif; ?>
            <a class="button button--secondary" href="<?= htmlspecialchars($projectsUrl, ENT_QUOTES, 'UTF-8') ?>">Ver todos os projetos</a>
        </div>
    </section>

    <?php require dirname(__DIR__) . '/partials/skills.php'; ?>

    <section id="formacao" class="home-section" aria-labelledby="formacao-titulo" tabindex="-1">
        <div class="section-heading">
            <p class="eyebrow">05 / Percurso</p>
            <h2 id="formacao-titulo">Formação e certificações</h2>
        </div>
        <div class="section-content">
            <article class="education" aria-labelledby="curso-titulo">
                <p class="education-period"><?= htmlspecialchars($profile['education']['period'], ENT_QUOTES, 'UTF-8') ?></p>
                <div class="education-heading">
                    <h3 id="curso-titulo"><?= htmlspecialchars($profile['education']['course'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <p class="education-status"><?= htmlspecialchars($profile['education']['status'], ENT_QUOTES, 'UTF-8') ?></p>
                </div>
                <p class="education-institution"><?= htmlspecialchars($profile['education']['institution'], ENT_QUOTES, 'UTF-8') ?></p>
            </article>
            <?php if ($profile['certifications'] !== []): ?>
                <section class="certifications" aria-labelledby="certificacoes-titulo">
                    <h3 id="certificacoes-titulo">Certificações</h3>
                    <ul class="certification-list" role="list">
                        <?php foreach ($profile['certifications'] as $certification): ?>
                            <li><?= htmlspecialchars($certification, ENT_QUOTES, 'UTF-8') ?></li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            <?php endif; ?>
        </div>
    </section>

    <?php require dirname(__DIR__) . '/partials/contact.php'; ?>
</div>
