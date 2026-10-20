<?php
/** @var list<array<string, mixed>> $projects */
/** @var string $basePath */
/** @var string $projectsUrl */
?>
<ul class="project-grid" role="list">
    <?php foreach ($projects as $project): ?>
        <li class="project-card">
            <?php if ($project['image'] !== null): ?>
                <img class="project-image" src="<?= htmlspecialchars($basePath . '/' . $project['image'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars('Prévia do projeto ' . $project['title'], ENT_QUOTES, 'UTF-8') ?>" loading="lazy" decoding="async" width="<?= (int) $project['image_width'] ?>" height="<?= (int) $project['image_height'] ?>">
            <?php endif; ?>
            <div class="project-card-content">
                <h3><a href="<?= htmlspecialchars($projectsUrl . '/' . rawurlencode($project['slug']), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8') ?></a></h3>
                <?php if ($project['status'] !== null): ?>
                    <p><?= htmlspecialchars($project['status'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
                <p><?= htmlspecialchars($project['summary'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php require __DIR__ . '/project-technologies.php'; ?>
                <?php require __DIR__ . '/project-links.php'; ?>
            </div>
        </li>
    <?php endforeach; ?>
</ul>
