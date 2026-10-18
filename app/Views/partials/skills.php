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
                    <div class="skill-category">
                        <h3><?= htmlspecialchars($group['category'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <ul class="skill-list">
                            <?php foreach ($group['items'] as $skill): ?>
                                <li><?= htmlspecialchars($skill, ENT_QUOTES, 'UTF-8') ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
