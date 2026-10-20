<?php
/** @var array<string, mixed> $project */
/** @var string $projectsUrl */
/** @var string $basePath */
?>
<article class="project-detail" aria-labelledby="projeto-titulo">
    <a class="text-link" href="<?= htmlspecialchars($projectsUrl, ENT_QUOTES, 'UTF-8') ?>"><span aria-hidden="true">&larr;</span> Voltar aos projetos</a>
    <div class="page-content">
        <p class="eyebrow">Portfólio / Projeto</p>
        <h1 id="projeto-titulo"><?= htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8') ?></h1>
        <?php if ($project['status'] !== null): ?>
            <p><?= htmlspecialchars($project['status'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        <p><?= htmlspecialchars($project['summary'], ENT_QUOTES, 'UTF-8') ?></p>
    </div>
    <?php if ($project['image'] !== null): ?>
        <img class="project-detail-image" src="<?= htmlspecialchars($basePath . '/' . $project['image'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars('Prévia do projeto ' . $project['title'], ENT_QUOTES, 'UTF-8') ?>" width="<?= (int) $project['image_width'] ?>" height="<?= (int) $project['image_height'] ?>" decoding="async">
    <?php endif; ?>
    <section class="section-content" aria-labelledby="projeto-descricao-titulo">
        <h2 id="projeto-descricao-titulo">Sobre o projeto</h2>
        <p class="project-description"><?= htmlspecialchars($project['description'], ENT_QUOTES, 'UTF-8') ?></p>
    </section>
    <?php if ($project['technologies'] !== []): ?>
        <section class="section-content" aria-labelledby="projeto-tecnologias-titulo">
            <h2 id="projeto-tecnologias-titulo">Tecnologias utilizadas</h2>
            <?php require dirname(__DIR__) . '/partials/project-technologies.php'; ?>
        </section>
    <?php endif; ?>
    <?php if ($project['github_url'] !== null || $project['demo_url'] !== null): ?>
        <section class="section-content" aria-labelledby="projeto-links-titulo">
            <h2 id="projeto-links-titulo">Acesse o projeto</h2>
            <?php require dirname(__DIR__) . '/partials/project-links.php'; ?>
        </section>
    <?php endif; ?>
</article>
