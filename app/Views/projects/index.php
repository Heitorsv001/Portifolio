<?php /** @var list<array<string, mixed>> $projects */ ?>
<div class="project-catalog">
    <div class="page-content">
        <p class="eyebrow">Portfólio / Projetos</p>
        <h1>Projetos</h1>
        <p>Conheça meus projetos acadêmicos e pessoais, suas tecnologias e limitações. Acesse os repositórios e as demonstrações disponíveis.</p>
    </div>
    <section aria-labelledby="catalogo-titulo">
        <h2 id="catalogo-titulo" class="catalog-heading">Catálogo de projetos</h2>
        <?php if ($projects === []): ?>
            <div class="project-notice">
                <p>Nenhum projeto cadastrado. Os projetos serão apresentados após a confirmação de suas informações pelo proprietário.</p>
            </div>
        <?php else: ?>
            <?php require dirname(__DIR__) . '/partials/project-list.php'; ?>
        <?php endif; ?>
    </section>
</div>
