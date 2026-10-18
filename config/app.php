<?php

declare(strict_types=1);

$basePath = getenv('APP_BASE_PATH');

return [
    'name' => 'Meu portfólio',
    // Mantém o acesso Apache existente; use APP_BASE_PATH='' com public/ como raiz.
    'base_path' => '/' . trim($basePath === false ? '/portifolio' : $basePath, '/'),
];
