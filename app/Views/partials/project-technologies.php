<?php /** @var array<string, mixed> $project */ ?>
<?php if ($project['technologies'] !== []): ?>
    <ul class="project-technologies" role="list" aria-label="Tecnologias utilizadas">
        <?php foreach ($project['technologies'] as $technology): ?>
            <li><?= htmlspecialchars($technology, ENT_QUOTES, 'UTF-8') ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
