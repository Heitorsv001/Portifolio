<?php /** @var array<string, mixed> $project */ ?>
<?php if ($project['github_url'] !== null || $project['demo_url'] !== null): ?>
    <div class="project-links">
        <?php if ($project['github_url'] !== null): ?>
            <a class="text-link" href="<?= htmlspecialchars($project['github_url'], ENT_QUOTES, 'UTF-8') ?>" aria-label="<?= htmlspecialchars('Repositório de ' . $project['title'], ENT_QUOTES, 'UTF-8') ?>">Repositório</a>
        <?php endif; ?>
        <?php if ($project['demo_url'] !== null): ?>
            <a class="text-link" href="<?= htmlspecialchars($project['demo_url'], ENT_QUOTES, 'UTF-8') ?>" aria-label="<?= htmlspecialchars('Demonstração de ' . $project['title'], ENT_QUOTES, 'UTF-8') ?>">Demonstração</a>
        <?php endif; ?>
    </div>
<?php endif; ?>
