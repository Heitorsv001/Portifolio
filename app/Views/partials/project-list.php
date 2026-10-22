<?php
/** @var list<array<string, mixed>> $projectItems */
/** @var string $projectLayout */
/** @var string $basePath */
/** @var string $projectsUrl */
$projectHeading = $projectLayout === 'catalog' ? 'h3' : 'h4';
?>
<ul class="project-list project-list--<?= htmlspecialchars($projectLayout, ENT_QUOTES, 'UTF-8') ?>" role="list">
    <?php foreach ($projectItems as $project): ?>
        <li class="project-card"<?= $project['image'] !== null && $projectLayout !== 'compact' ? ' data-project-image' : '' ?>>
            <?php if ($project['image'] !== null && $projectLayout !== 'compact'): ?>
                <img class="project-image" src="<?= htmlspecialchars($basePath . '/' . $project['image'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars('Prévia do projeto ' . $project['title'], ENT_QUOTES, 'UTF-8') ?>" loading="lazy" decoding="async" width="<?= (int) $project['image_width'] ?>" height="<?= (int) $project['image_height'] ?>">
            <?php endif; ?>
            <div class="project-card-content">
                <<?= $projectHeading ?> class="project-card-heading"><a href="<?= htmlspecialchars($projectsUrl . '/' . rawurlencode($project['slug']), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8') ?></a></<?= $projectHeading ?>>
                <?php if ($project['status'] !== null): ?>
                    <p class="project-status"><?= htmlspecialchars($project['status'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
                <p class="project-summary"><?= htmlspecialchars($project['summary'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php require __DIR__ . '/project-technologies.php'; ?>
                <?php require __DIR__ . '/project-links.php'; ?>
            </div>
        </li>
    <?php endforeach; ?>
</ul>
