<?php

declare(strict_types=1);

namespace App\Models;

final class Skill
{
    /** @var list<array{category: string, items: list<string>}> */
    private readonly array $groups;

    public function __construct(?string $dataFile = null)
    {
        $groups = require ($dataFile ?? dirname(__DIR__, 2) . '/config/skills.php');
        $normalized = [];

        foreach ($groups as $group) {
            $category = trim($group['category']);
            $items = array_values(array_filter(
                array_map('trim', $group['items']),
                static fn (string $item): bool => $item !== ''
            ));

            if ($category !== '' && $items !== []) {
                $normalized[] = ['category' => $category, 'items' => $items];
            }
        }

        $this->groups = $normalized;
    }

    /** @return list<array{category: string, items: list<string>}> */
    public function all(): array
    {
        return $this->groups;
    }
}
