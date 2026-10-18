<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Project;
use App\Models\Skill;

final class HomeController extends Controller
{
    /** @param array{name: string, base_path: string} $config */
    public function __construct(
        array $config,
        private readonly Project $projects = new Project(),
        private readonly Skill $skills = new Skill()
    ) {
        parent::__construct($config);
    }

    public function index(): void
    {
        $this->view('home/index', [
            'currentPage' => 'home',
            'projects' => $this->projects->featured(),
            'skillGroups' => $this->skills->all(),
        ]);
    }
}
