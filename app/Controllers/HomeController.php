<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Contact;
use App\Models\Project;
use App\Models\Profile;
use App\Models\Skill;

final class HomeController extends Controller
{
    /** @param array{name: string, base_path: string, origin?: ?string} $config */
    public function __construct(
        array $config,
        private readonly Project $projects = new Project(),
        private readonly Skill $skills = new Skill(),
        private readonly Contact $contact = new Contact(),
        private readonly Profile $profile = new Profile()
    ) {
        parent::__construct($config);
    }

    public function index(): void
    {
        $profile = $this->profile->all();
        $featuredProjects = [];
        $otherProjects = [];
        foreach ($this->projects->all() as $project) {
            if ($project['featured'] === true) {
                $featuredProjects[] = $project;
            } else {
                $otherProjects[] = $project;
            }
        }
        $this->view('home/index', [
            'title' => $profile['headline'] . ' — ' . $profile['name'],
            'description' => 'Portfólio de ' . $profile['name'] . ', desenvolvedor Web Full Stack e estudante de Técnico Integrado em Informática no IFMS. Conheça projetos, formação e experiência de estágio.',
            'pagePath' => '/',
            'currentPage' => 'home',
            'projects' => $featuredProjects,
            'otherProjects' => $otherProjects,
            'skillGroups' => $this->skills->all(),
            'contact' => $this->contact->all(),
            'profile' => $profile,
        ]);
    }
}
