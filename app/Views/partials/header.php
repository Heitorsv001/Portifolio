<?php
/** @var string $appName */
/** @var string $homeUrl */
/** @var string $currentPage */
?>
<header class="site-header<?= $currentPage === 'home' ? ' site-header--home' : '' ?>">
    <div class="container header-content">
        <a class="brand" href="<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8') ?>"<?= $currentPage === 'home' ? ' aria-current="page"' : '' ?>>
            <span class="brand-mark" aria-hidden="true">&lt;/&gt;</span>
            <span><?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8') ?></span>
        </a>
        <div class="header-controls">
            <button class="theme-toggle" type="button" data-theme-toggle aria-label="Alternar para tema claro" title="Alternar para tema claro" hidden>
                <svg class="theme-icon--sun" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false">
                    <circle cx="12" cy="12" r="4"></circle>
                    <path d="M12 2v2m0 16v2M2 12h2m16 0h2M4.9 4.9l1.4 1.4m11.4 11.4 1.4 1.4M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"></path>
                </svg>
                <svg class="theme-icon--moon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false">
                    <path d="M20.9 13.3A9 9 0 0 1 10.7 3.1a9 9 0 1 0 10.2 10.2Z"></path>
                </svg>
            </button>
            <button class="menu-toggle" type="button" data-menu-toggle aria-expanded="false" aria-controls="navegacao-principal" aria-label="Abrir menu" hidden>
                <svg class="menu-icon--open" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false">
                    <path d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
                <svg class="menu-icon--close" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false">
                    <path d="m6 6 12 12M6 18 18 6"></path>
                </svg>
            </button>
        </div>
        <nav id="navegacao-principal" class="site-navigation" aria-label="Navegação principal">
            <ul class="nav-list" role="list">
                <li>
                    <a class="nav-link" href="<?= htmlspecialchars($homeUrl . '#experiencia', ENT_QUOTES, 'UTF-8') ?>">Experiência</a>
                </li>
                <li>
                    <a class="nav-link" href="<?= htmlspecialchars($homeUrl . '#sobre', ENT_QUOTES, 'UTF-8') ?>">Sobre mim</a>
                </li>
                <li>
                    <a class="nav-link" href="<?= htmlspecialchars($homeUrl . '#projetos', ENT_QUOTES, 'UTF-8') ?>">Projetos</a>
                </li>
                <li>
                    <a class="nav-link" href="<?= htmlspecialchars($homeUrl . '#habilidades', ENT_QUOTES, 'UTF-8') ?>">Habilidades</a>
                </li>
                <li>
                    <a class="nav-link" href="<?= htmlspecialchars($homeUrl . '#formacao', ENT_QUOTES, 'UTF-8') ?>">Formação</a>
                </li>
                <li>
                    <a class="nav-link" href="<?= htmlspecialchars($homeUrl . '#contato', ENT_QUOTES, 'UTF-8') ?>">Contato</a>
                </li>
            </ul>
        </nav>
    </div>
</header>
