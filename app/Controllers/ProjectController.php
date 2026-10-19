<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Project;

final class ProjectController extends Controller
{
    /** @param array{name: string, base_path: string, origin?: ?string} $config */
    public function __construct(array $config, private readonly Project $projects = new Project())
    {
        parent::__construct($config);
    }

    public function index(): void
    {
        $this->view('projects/index', [
            'title' => 'Projetos — ' . $this->config['name'],
            'description' => 'Explore o catálogo de projetos, suas tecnologias, descrições e links de repositório e demonstração disponíveis.',
            'pagePath' => '/projetos',
            'currentPage' => 'projects',
            'projects' => $this->projects->all(),
        ]);
    }

    public function show(string $slug): void
    {
        $project = $this->projects->findBySlug($slug);
        if ($project === null) {
            (new ErrorController($this->config))->notFound();
            return;
        }

        $this->view('projects/show', [
            'title' => $project['title'] . ' — ' . $this->config['name'],
            'description' => $project['summary'],
            'pagePath' => '/projetos/' . rawurlencode($project['slug']),
            'shareImage' => $project['image'],
            'shareImageAlt' => $project['image'] !== null
                ? 'Prévia do projeto ' . $project['title']
                : 'Portfólio — Desenvolvedor Full Stack',
            'project' => $project,
        ]);
    }
}
