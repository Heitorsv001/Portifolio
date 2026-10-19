<?php

declare(strict_types=1);

namespace App\Models;

final class Contact
{
    /** @var array{github_url: ?string, linkedin_url: ?string, email: ?string, resume: ?string} */
    private readonly array $contact;

    public function __construct(?string $dataFile = null)
    {
        $contact = require ($dataFile ?? dirname(__DIR__, 2) . '/config/contact.php');
        $email = trim($contact['email'] ?? '');

        $this->contact = [
            'github_url' => $this->externalUrl($contact['github_url'] ?? null),
            'linkedin_url' => $this->externalUrl($contact['linkedin_url'] ?? null),
            'email' => filter_var($email, FILTER_VALIDATE_EMAIL) !== false ? $email : null,
            'resume' => $this->resumePath($contact['resume'] ?? null),
        ];
    }

    /** @return array{github_url: ?string, linkedin_url: ?string, email: ?string, resume: ?string} */
    public function all(): array
    {
        return $this->contact;
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

    private function resumePath(?string $path): ?string
    {
        if ($path === null
            || !preg_match('~^assets/documents/(?:[a-zA-Z0-9_-]+/)*[a-zA-Z0-9_-]+\.pdf$~D', $path)) {
            return null;
        }

        $file = realpath(dirname(__DIR__, 2) . '/public/' . $path);
        $directory = dirname(__DIR__, 2) . '/public/assets/documents/';
        if ($file === false || !str_starts_with($file, $directory) || !is_file($file)) {
            return null;
        }

        return $path;
    }
}
