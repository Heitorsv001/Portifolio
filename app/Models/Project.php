<?php

declare(strict_types=1);

namespace App\Models;

final class Project
{
    /** @var list<array<string, mixed>> */
    private readonly array $projects;

    public function __construct(?string $dataFile = null)
    {
        $projects = require ($dataFile ?? dirname(__DIR__, 2) . '/config/projects.php');
        $projects = array_filter(
            $projects,
            static fn (array $project): bool => ($project['visible'] ?? true) === true
        );

        foreach ($projects as &$project) {
            $project += [
                'technologies' => [],
                'image' => null,
                'github_url' => null,
                'demo_url' => null,
                'featured' => false,
                'status' => null,
            ];
            $project['github_url'] = $this->externalUrl($project['github_url']);
            $project['demo_url'] = $this->externalUrl($project['demo_url']);
            $project['image'] = $this->imagePath($project['image']);
            $imageSize = $project['image'] !== null
                ? @getimagesize(dirname(__DIR__, 2) . '/public/' . $project['image'])
                : false;
            if ($imageSize === false) {
                $project['image'] = null;
            }
            $project['image_width'] = $imageSize !== false ? $imageSize[0] : null;
            $project['image_height'] = $imageSize !== false ? $imageSize[1] : null;
            $project['technologies'] ??= [];
        }
        unset($project);

        $this->projects = array_values($projects);
    }

    /** @return list<array<string, mixed>> */
    public function all(): array
    {
        return $this->projects;
    }

    /** @return list<array<string, mixed>> */
    public function featured(): array
    {
        return array_values(array_filter(
            $this->projects,
            static fn (array $project): bool => $project['featured'] === true
        ));
    }

    /** @return array<string, mixed>|null */
    public function findBySlug(string $slug): ?array
    {
        if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/D', $slug)) {
            return null;
        }

        foreach ($this->projects as $project) {
            if ($project['slug'] === $slug) {
                return $project;
            }
        }

        return null;
    }

    private function externalUrl(?string $url): ?string
    {
        if ($url === null || filter_var($url, FILTER_VALIDATE_URL) === false) {
            return null;
        }

        $parts = parse_url($url);
        if (!in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true)
            || isset($parts['user']) || isset($parts['pass'])) {
            return null;
        }

        return $url;
    }

    private function imagePath(?string $path): ?string
    {
        if ($path === null
            || !preg_match('~^assets/images/[a-zA-Z0-9_/-]+\.(?:png|jpe?g|webp|gif|avif)$~D', $path)
            || !is_file(dirname(__DIR__, 2) . '/public/' . $path)) {
            return null;
        }

        return $path;
    }
}
