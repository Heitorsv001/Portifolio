<?php
/** @var string $appName */
/** @var string $homeUrl */
/** @var string $currentPage */
?>
<header class="site-header">
    <div class="container header-content">
        <a class="brand" href="<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8') ?>">
            <span class="brand-mark" aria-hidden="true">&lt;/&gt;</span>
            <span><?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8') ?></span>
        </a>
        <nav aria-label="Navegação principal">
            <ul class="nav-list">
                <li>
                    <a class="nav-link" href="<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8') ?>"<?= $currentPage === 'home' ? ' aria-current="page"' : '' ?>>Início</a>
                </li>
                <li>
                    <a class="nav-link" href="<?= htmlspecialchars($homeUrl . '#sobre', ENT_QUOTES, 'UTF-8') ?>">Sobre mim</a>
                </li>
                <li>
                    <a class="nav-link" href="<?= htmlspecialchars($homeUrl . '#objetivos', ENT_QUOTES, 'UTF-8') ?>">Objetivos</a>
                </li>
                <li>
                    <a class="nav-link" href="<?= htmlspecialchars($homeUrl . '#projetos', ENT_QUOTES, 'UTF-8') ?>">Projetos</a>
                </li>
            </ul>
        </nav>
    </div>
</header>
