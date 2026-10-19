<?php
/** @var array{github_url: ?string, linkedin_url: ?string, email: ?string, resume: ?string} $contact */
/** @var string $basePath */
?>
<section id="contato" class="home-section" aria-labelledby="contato-titulo" tabindex="-1">
    <div class="section-heading">
        <p class="eyebrow">05 / Vamos conversar</p>
        <h2 id="contato-titulo">Contato e redes profissionais</h2>
    </div>
    <div class="section-content">
        <?php if ($contact['github_url'] === null && $contact['linkedin_url'] === null && $contact['email'] === null && $contact['resume'] === null): ?>
            <p>Contatos a confirmar. Os links profissionais e o e-mail serão disponibilizados após a confirmação do proprietário.</p>
        <?php else: ?>
            <?php if ($contact['github_url'] !== null || $contact['linkedin_url'] !== null || $contact['email'] !== null): ?>
                <ul class="contact-links">
                    <?php if ($contact['github_url'] !== null): ?>
                        <li><a class="text-link" href="<?= htmlspecialchars($contact['github_url'], ENT_QUOTES, 'UTF-8') ?>">Perfil no GitHub</a></li>
                    <?php endif; ?>
                    <?php if ($contact['linkedin_url'] !== null): ?>
                        <li><a class="text-link" href="<?= htmlspecialchars($contact['linkedin_url'], ENT_QUOTES, 'UTF-8') ?>">Perfil no LinkedIn</a></li>
                    <?php endif; ?>
                    <?php if ($contact['email'] !== null): ?>
                        <li><a class="text-link" href="<?= htmlspecialchars('mailto:' . str_replace('%40', '@', rawurlencode($contact['email'])), ENT_QUOTES, 'UTF-8') ?>">Enviar e-mail para <?= htmlspecialchars($contact['email'], ENT_QUOTES, 'UTF-8') ?></a></li>
                    <?php endif; ?>
                </ul>
            <?php endif; ?>
            <?php if ($contact['resume'] !== null): ?>
                <a class="button button--secondary" href="<?= htmlspecialchars($basePath . '/' . $contact['resume'], ENT_QUOTES, 'UTF-8') ?>" download>Baixar currículo (PDF)</a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>
