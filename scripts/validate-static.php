<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require __DIR__ . '/static-validation.php';

try {
    \PortfolioStatic\strictErrors();
    $target = \PortfolioStatic\target(array_slice($argv, 1));
    $settings = \PortfolioStatic\settings();
    $result = \PortfolioStatic\validateArtifact($settings['root'] . '/dist', $settings, $target);
    echo 'Artefato válido: ' . $result['pages'] . ' páginas públicas, 404, ' . $result['assets'] . ' assets e '
        . count($result['files']) . " arquivos.\n";
} catch (Throwable $exception) {
    fwrite(STDERR, 'Validação interrompida: ' . $exception->getMessage() . PHP_EOL);
    exit(1);
}
