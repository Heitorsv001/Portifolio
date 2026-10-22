<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require __DIR__ . '/static-support.php';

try {
    \PortfolioStatic\strictErrors();
    if ($argc !== 2 || !str_starts_with($argv[1], '/')) {
        throw new RuntimeException('O adaptador exige uma URI local.');
    }
    putenv('APP_STATIC_EXPORT=1');
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = $argv[1];
    http_response_code(200);
    ob_start();
    require dirname(__DIR__) . '/public/index.php';
    $html = ob_get_clean();
    echo json_encode(['status' => http_response_code(), 'html' => $html], JSON_THROW_ON_ERROR);
} catch (Throwable $exception) {
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    fwrite(STDERR, $exception->getMessage() . PHP_EOL);
    exit(1);
}
