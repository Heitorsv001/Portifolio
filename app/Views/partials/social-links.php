<?php /** @var array{github_url: ?string, linkedin_url: ?string, email: ?string, resume: ?string} $contact */ ?>
<?php if ($contact['github_url'] !== null || $contact['linkedin_url'] !== null): ?>
    <ul class="social-links" role="list" aria-label="Redes profissionais">
        <?php if ($contact['github_url'] !== null): ?>
            <li>
                <a href="<?= htmlspecialchars($contact['github_url'], ENT_QUOTES, 'UTF-8') ?>" aria-label="Perfil no GitHub">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                        <path d="M8 21v-3c-3 .5-4-1-5-2m13 5v-4c0-.8-.3-1.5-.8-2 3.3-.4 5.8-1.8 5.8-5a4.5 4.5 0 0 0-1.2-3c.2-1.1.2-2.2-.4-3-1.3 0-2.5.7-3.4 1.3a12 12 0 0 0-8 0C7.1 4.7 5.9 4 4.6 4c-.6.8-.6 1.9-.4 3A4.5 4.5 0 0 0 3 10c0 3.2 2.5 4.6 5.8 5-.5.5-.8 1.2-.8 2"></path>
                    </svg>
                </a>
            </li>
        <?php endif; ?>
        <?php if ($contact['linkedin_url'] !== null): ?>
            <li>
                <a href="<?= htmlspecialchars($contact['linkedin_url'], ENT_QUOTES, 'UTF-8') ?>" aria-label="Perfil no LinkedIn">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                        <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                        <path d="M7.5 10.5v7m4-7v7m0-4a3 3 0 0 1 6 0v4"></path>
                        <circle cx="7.5" cy="7.5" r=".5" fill="currentColor"></circle>
                    </svg>
                </a>
            </li>
        <?php endif; ?>
    </ul>
<?php endif; ?>
