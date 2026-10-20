<?php

declare(strict_types=1);

namespace App\Models;

final class Profile
{
    /** @var array<string, mixed> */
    private readonly array $profile;

    public function __construct(?string $dataFile = null)
    {
        $this->profile = require ($dataFile ?? dirname(__DIR__, 2) . '/config/profile.php');
    }

    /** @return array<string, mixed> */
    public function all(): array
    {
        return $this->profile;
    }
}
