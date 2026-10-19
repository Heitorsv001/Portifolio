<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Contact;
use App\Models\Project;
use App\Models\Skill;

final class HomeController extends Controller
{
    /** @param array{name: string, base_path: string, origin?: ?string} $config */
    public function __construct(
        array $config,
        private readonly Project $projects = new Project(),
        private readonly Skill $skills = new Skill(),
        private readonly Contact $contact = new Contact()
    ) {
        parent::__construct($config);
    }

    public function index(): void
    {
        $this->view('home/index', [
            'title' => 'Desenvolvedor Full Stack — ' . $this->config['name'],
            'description' => 'Apresentação, projetos, habilidades e canais de contato de um Desenvolvedor Full Stack.',
            'pagePath' => '/',
            'currentPage' => 'home',
            'projects' => $this->projects->featured(),
            'skillGroups' => $this->skills->all(),
            'contact' => $this->contact->all(),
        ]);
    }
}
