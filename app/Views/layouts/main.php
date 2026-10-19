<?php
/** @var string $title */
/** @var string $description */
/** @var bool $noIndex */
/** @var string|null $canonicalUrl */
/** @var string|null $shareImageUrl */
/** @var array|false $shareImageSize */
/** @var string $shareImageAlt */
/** @var string $stylesheetUrl */
/** @var string $file Caminho interno da view, definido pelo Controller. */
?>
<!doctype html>
<html lang="pt-BR" prefix="og: https://ogp.me/ns#">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="description" content="<?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?>">
    <?php if ($noIndex): ?>
        <meta name="robots" content="noindex, follow">
    <?php else: ?>
        <meta property="og:title" content="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>">
        <meta property="og:description" content="<?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?>">
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="<?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8') ?>">
        <meta property="og:locale" content="pt_BR">
        <?php if ($canonicalUrl !== null): ?>
            <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8') ?>">
            <meta property="og:url" content="<?= htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8') ?>">
        <?php endif; ?>
        <?php if ($shareImageUrl !== null): ?>
            <meta property="og:image" content="<?= htmlspecialchars($shareImageUrl, ENT_QUOTES, 'UTF-8') ?>">
            <meta property="og:image:alt" content="<?= htmlspecialchars($shareImageAlt, ENT_QUOTES, 'UTF-8') ?>">
            <meta property="og:image:width" content="<?= (int) $shareImageSize[0] ?>">
            <meta property="og:image:height" content="<?= (int) $shareImageSize[1] ?>">
            <meta property="og:image:type" content="<?= htmlspecialchars($shareImageSize['mime'], ENT_QUOTES, 'UTF-8') ?>">
        <?php endif; ?>
    <?php endif; ?>
    <link rel="stylesheet" href="<?= htmlspecialchars($stylesheetUrl, ENT_QUOTES, 'UTF-8') ?>">
</head>
<body id="topo">
    <a class="skip-link" href="#conteudo">Pular para o conteúdo</a>
    <?php require dirname(__DIR__) . '/partials/header.php'; ?>
    <main id="conteudo" class="site-main container" tabindex="-1">
        <?php require $file; ?>
    </main>
    <?php require dirname(__DIR__) . '/partials/footer.php'; ?>
</body>
</html>
