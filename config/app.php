<?php

declare(strict_types=1);

$basePath = getenv('APP_BASE_PATH');
$origin = getenv('APP_ORIGIN');

return [
    'name' => 'Heitor Silva Vareiro',
    // Mantém o acesso Apache existente; use APP_BASE_PATH='' com public/ como raiz.
    'base_path' => '/' . trim($basePath === false ? '/portifolio' : $basePath, '/'),
    // Origem pública confirmada, sem caminho (ex.: https://portfolio.example).
    // Sem domínio definido, não emite URLs absolutas de SEO/compartilhamento.
    'origin' => $origin === false ? null : $origin,
    'static_export' => PHP_SAPI === 'cli' && getenv('APP_STATIC_EXPORT') === '1',
];
