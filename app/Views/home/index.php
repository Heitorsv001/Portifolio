<?php
/** @var list<array<string, mixed>> $projects */
/** @var string $projectsUrl */
/** @var array<string, mixed> $profile */
?>
<section class="home-hero" aria-labelledby="apresentacao-titulo">
    <p class="eyebrow"><?= htmlspecialchars($profile['headline'], ENT_QUOTES, 'UTF-8') ?></p>
    <h1 id="apresentacao-titulo"><span class="hero-highlight"><?= htmlspecialchars($profile['name'], ENT_QUOTES, 'UTF-8') ?></span></h1>
    <p class="hero-name"><?= htmlspecialchars($profile['student_status'], ENT_QUOTES, 'UTF-8') ?></p>
    <p class="home-intro"><?= htmlspecialchars($profile['intro'], ENT_QUOTES, 'UTF-8') ?></p>
    <div class="hero-actions">
        <a class="button" href="#projetos">Conheça meus projetos <span aria-hidden="true">&darr;</span></a>
        <a class="button button--secondary" href="#sobre">Sobre mim</a>
        <?php if ($contact['github_url'] !== null): ?>
            <a class="button button--secondary" href="<?= htmlspecialchars($contact['github_url'], ENT_QUOTES, 'UTF-8') ?>">GitHub</a>
        <?php endif; ?>
        <?php if ($contact['linkedin_url'] !== null): ?>
            <a class="button button--secondary" href="<?= htmlspecialchars($contact['linkedin_url'], ENT_QUOTES, 'UTF-8') ?>">LinkedIn</a>
        <?php endif; ?>
    </div>
</section>

<section id="sobre" class="home-section" aria-labelledby="sobre-titulo" tabindex="-1">
    <div class="section-heading">
        <p class="eyebrow">01 / Apresentação</p>
        <h2 id="sobre-titulo">Sobre mim</h2>
    </div>
    <div class="section-content">
        <?php foreach ($profile['about'] as $paragraph): ?>
            <p><?= htmlspecialchars($paragraph, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endforeach; ?>
        <h3>Formação</h3>
        <dl class="profile-details">
            <div>
                <dt><?= htmlspecialchars($profile['education']['course'], ENT_QUOTES, 'UTF-8') ?></dt>
                <dd><?= htmlspecialchars($profile['education']['institution'], ENT_QUOTES, 'UTF-8') ?></dd>
                <dd><?= htmlspecialchars($profile['education']['period'], ENT_QUOTES, 'UTF-8') ?></dd>
                <dd><?= htmlspecialchars($profile['education']['status'], ENT_QUOTES, 'UTF-8') ?></dd>
            </div>
        </dl>
        <h3>Experiência profissional</h3>
        <dl class="profile-details">
            <div>
                <dt><?= htmlspecialchars($profile['experience']['role'], ENT_QUOTES, 'UTF-8') ?></dt>
                <dd><?= htmlspecialchars($profile['experience']['institution'], ENT_QUOTES, 'UTF-8') ?></dd>
                <dd><?= htmlspecialchars($profile['experience']['period'], ENT_QUOTES, 'UTF-8') ?></dd>
                <dd><?= htmlspecialchars($profile['experience']['project'], ENT_QUOTES, 'UTF-8') ?></dd>
                <dd><?= htmlspecialchars($profile['experience']['description'], ENT_QUOTES, 'UTF-8') ?></dd>
            </div>
        </dl>
        <h3>Certificações</h3>
        <ul class="skill-list">
            <?php foreach ($profile['certifications'] as $certification): ?>
                <li><?= htmlspecialchars($certification, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>

<section id="objetivos" class="home-section" aria-labelledby="objetivos-titulo" tabindex="-1">
    <div class="section-heading">
        <p class="eyebrow">02 / Próximos passos</p>
        <h2 id="objetivos-titulo">Objetivos profissionais</h2>
    </div>
    <div class="section-content">
        <p><?= htmlspecialchars($profile['goals'], ENT_QUOTES, 'UTF-8') ?></p>
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

<?php require dirname(__DIR__) . '/partials/contact.php'; ?>
