<?php /** @var list<array{category: string, items: list<string>}> $skillGroups */ ?>
<section id="habilidades" class="home-section" aria-labelledby="habilidades-titulo" tabindex="-1">
    <div class="section-heading">
        <p class="eyebrow">04 / Conhecimentos</p>
        <h2 id="habilidades-titulo">Habilidades e tecnologias</h2>
    </div>
    <div class="section-content">
        <?php if ($skillGroups === []): ?>
            <p>Habilidades a confirmar. Os conhecimentos serão apresentados por categoria após a confirmação do proprietário.</p>
        <?php else: ?>
            <div class="skills-grid">
                <?php foreach ($skillGroups as $group): ?>
                    <?php
                    $skillIconPath = match ($group['category']) {
                        'Linguagens' => 'M8 7 3 12l5 5m8-10 5 5-5 5M14 4l-4 16',
                        'Front-end web' => 'M3 5h18v14H3zM3 9h18M8 12l-2 2 2 2m8-4 2 2-2 2',
                        'Back-end' => 'M4 3h16v7H4zM4 14h16v7H4zM8 6.5h.01M8 17.5h.01M12 6.5h4M12 17.5h4',
                        'Banco de dados e acesso a dados' => 'M4 6c0-4 16-4 16 0s-16 4-16 0v12c0 4 16 4 16 0V6M4 12c0 4 16 4 16 0',
                        'Ferramentas' => 'M4 4l16 16m0-16L4 20M3 3l4 1-3 3zM17 17l4 1-3 3z',
                        'Arquitetura e práticas' => 'M3 3h7v7H3zM14 3h7v7h-7zM8.5 14h7v7h-7zM6.5 10v2h11v-2M12 12v2',
                        'Ferramentas e agentes de IA' => 'M7 7h10v10H7zM9 3v4m6-4v4M9 17v4m6-4v4M3 9h4m-4 6h4m10-6h4m-4 6h4M10 10h4v4h-4z',
                        default => 'M4 12l5 5L20 6',
                    };
                    ?>
                    <div class="skill-category">
                        <h3><?= htmlspecialchars($group['category'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <ul class="skill-list" role="list">
                            <?php foreach ($group['items'] as $skill): ?>
                                <li>
                                    <svg class="skill-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                                        <path d="<?= htmlspecialchars($skillIconPath, ENT_QUOTES, 'UTF-8') ?>"></path>
                                    </svg>
                                    <span class="skill-name"><?= htmlspecialchars($skill, ENT_QUOTES, 'UTF-8') ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
