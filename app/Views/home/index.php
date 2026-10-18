<?php
/** @var string $title */
/** @var string $stylesheetUrl */
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="<?= htmlspecialchars($stylesheetUrl, ENT_QUOTES, 'UTF-8') ?>">
</head>
<body>
    <main class="container">
        <h1><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h1>
        <p>Estrutura MVC em PHP pronta para começar.</p>
    </main>
</body>
</html>
