<?php
/** @var string $title */
/** @var string $stylesheetUrl */
/** @var string $file Caminho interno da view, definido pelo Controller. */
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
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
