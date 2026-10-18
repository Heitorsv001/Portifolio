<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

final class HomeController extends Controller
{
    /** @param array{name: string, base_path: string} $config */
    public function __construct(private readonly array $config)
    {
    }

    public function index(): void
    {
        $this->view('home/index', [
            'title' => $this->config['name'],
            'stylesheetUrl' => rtrim($this->config['base_path'], '/') . '/assets/css/style.css',
        ]);
    }
}
