# Portfólio MVC em PHP

Site de portfólio pessoal em PHP, com arquitetura MVC própria, catálogo e páginas individuais de projetos. A aplicação renderiza HTML no servidor e utiliza arquivos PHP de configuração como fonte dos dados, sem framework ou banco de dados.

Portfólio de Heitor Silva Vareiro, Desenvolvedor Web Full Stack e estudante de Técnico Integrado em Informática no IFMS. Conteúdo profissional preenchido; publicação ainda pendente.

## Conteúdo aprovado — 19/10/2026

Povoamento baseado em `PORTFOLIO_CONTENT_GUIDE_ATUALIZADO.md` (nome disponível do guia solicitado como `PORTFOLIO_CONTENT_GUIDE.md`), currículo local e READMEs dos cinco repositórios ativos, com conferência dos trechos de código relevantes. O guia existente foi preservado. Não foram executadas as aplicações dos outros repositórios nem suas integrações.

- `config/profile.php`, consultado pelo Model `Profile`, concentra apresentação, formação, estágio, objetivos e as quatro certificações. A formação continua em andamento, com término previsto para dezembro de 2026. O estágio registra março a outubro de 2026 e o Arco Literário como experiência no IFMS, sem card de repositório pessoal. Certificações contêm apenas nomes.
- Cinco projetos cadastrados, quatro visíveis: Gerenciador de Permanências, Crowns, Festividades de Fim de Ano e Notícias. Gerenciador e Crowns aparecem em destaque. Os status editoriais são mantidos em `config/projects.php`, incluindo Crowns como descontinuado, preservando as descrições das limitações técnicas. Festividades e Notícias são secundários.
- Portfólio MVC em PHP está preenchido com `visible => false` e `featured => false` até a confirmação da publicação. Um projeto oculto é excluído de `all()`, `featured()` e `findBySlug()`; sua URL retorna 404. Após confirmar a publicação, habilite `visible` e selecione o destaque em `config/projects.php`.
- `status` é texto opcional, escapado nos cards e detalhes, para informar o estágio e as limitações do projeto. Não se confunde com `featured` ou `visible`.
- Festividades e Notícias têm links de demonstração cadastrados. A demo de Festividades, `https://heitorsv001.github.io/festividades/`, respondeu HTTP 200 na validação do povoamento; a demo de Notícias foi incluída pelo proprietário posteriormente. O formulário apenas simula o envio. Todas as imagens continuam nulas, com a área omitida; nenhuma ilustração foi apresentada como captura real.
- Habilidades agrupadas conforme currículo e projetos, sem porcentagens; GitHub e LinkedIn preenchidos. E-mail continua nulo, pois o guia exige autorização específica para divulgação; PDF de currículo não fornecido.

Validação do povoamento: **986/986** verificações com `E_ALL`, sintaxe dos **29 arquivos PHP** e `git diff --check` aprovados. Integração HTTP aprovada no Apache com `/portifolio` e em servidor PHP temporário com raiz `public/`: páginas 200, projeto oculto/rotas inválidas/POST 404, assets corretos, navegação e metadados válidos, arquivos privados inacessíveis. Servidor temporário encerrado. Navegador conectado indisponível; revisão visual responsiva, console, zoom e teclado do conteúdo preenchido não foram executados. Os resultados Lighthouse em `PROGRESS.md` pertencem à etapa 8 anterior.

Pendências técnicas documentadas: compilação e persistência do Crowns; testes de interação dos projetos; capturas reais; dados complementares dos certificados; autorização do e-mail e PDF aprovado; domínio, hospedagem, `APP_ORIGIN`, publicação, robots/sitemap e preview público. A etapa de povoamento foi realizada sem commits ou pushes automáticos.

## Funcionalidades

- Página inicial com apresentação, sobre, objetivos, projetos em destaque, habilidades e contato.
- Catálogo de projetos e páginas individuais acessíveis por slug.
- Layout, navegação e componentes compartilhados entre as Views.
- Estados vazios para dados não preenchidos e omissão de imagens indisponíveis.
- Validação de URLs externas, imagens locais e caminho do currículo; escaping do conteúdo dinâmico nas Views.
- Página 404 com status HTTP correspondente para rotas e projetos não encontrados.
- Títulos e descrições por página, metadados Open Graph e URLs canônicas quando a origem pública é configurada.
- CSS com grid fluido, tipografia adaptável, foco visível, link para pular ao conteúdo e suporte à navegação por teclado na marcação.

Não há login, painel administrativo, CRUD pela interface ou envio de formulário de contato. Os links de e-mail usam `mailto:` para abrir o aplicativo do visitante.

## Tecnologias e arquitetura

- **PHP 8.2 ou superior**, conforme `composer.json`.
- **Composer**, usado para autoload PSR-4 de `App\` para `app/`, sem bibliotecas externas de execução.
- **HTML e CSS**, sem JavaScript, framework de interface ou fontes remotas.
- **Apache e mod_rewrite**, para o modo de acesso por `.htaccess`; o servidor embutido do PHP é uma alternativa de desenvolvimento.
- **Testes em PHP**, com script próprio e sem framework de testes.

O front controller registra as rotas no Router. Os Controllers consultam Models e selecionam Views; Models leem e normalizam os dados dos arquivos de configuração. Layout e partials concentram a marcação compartilhada.

```text
app/
├── Controllers/       # Home, catálogo, detalhe e erro
├── Core/              # Router e Controller base
├── Models/            # Perfil, projetos, habilidades e contatos
└── Views/             # Páginas, layout e partials
config/                # Aplicação e conteúdo do portfólio
public/
├── index.php          # Front controller
└── assets/            # CSS, imagens e documentos públicos
storage/logs/          # Diretório reservado para logs
tests/run.php          # Verificações isoladas
composer.json
composer.lock
```

## Instalação e execução

Requisitos: PHP 8.2 ou superior e Composer 2. Não é necessário configurar banco de dados ou serviços externos.

```sh
git clone https://github.com/Heitorsv001/Portifolio.git
cd Portifolio
composer install
APP_BASE_PATH='' php -S 127.0.0.1:8000 -t public
```

Os comandos usam um shell POSIX. Acesse `http://127.0.0.1:8000/`; encerre com `Ctrl+C`. O Composer gera `vendor/autoload.php`. O servidor PHP embutido é destinado ao desenvolvimento.

### Configuração do endereço

| Variável | Padrão | Finalidade |
| --- | --- | --- |
| `APP_BASE_PATH` | `/portifolio` | Prefixo da URL; use vazio ao servir na raiz com `public/` como raiz de documentos |
| `APP_ORIGIN` | Ausente | Origem HTTP(S) para canonical, `og:url` e `og:image` |

As variáveis são lidas pelo processo PHP em `config/app.php`. Não há carregador de `.env`. O funcionamento local não exige `APP_ORIGIN`; quando ela está ausente ou é inválida, os metadados textuais permanecem, mas as URLs absolutas de compartilhamento são omitidas.

`APP_ORIGIN` deve conter somente esquema, host e porta opcional, sem caminho, credenciais, query string ou fragmento. Subdiretórios são definidos em `APP_BASE_PATH`.

### Apache e hospedagem

O acesso em `http://localhost/portifolio/` é previsto pelo `.htaccess` da raiz, que contém referências específicas a esse caminho. Ele exige PHP habilitado no Apache, `mod_rewrite` e permissão para processar `.htaccess`. Alterar apenas a variável de ambiente não adapta essas referências da instalação na raiz do repositório a outro subdiretório.

Para hospedagem, configure a raiz de documentos como `public/`, encaminhe rotas não correspondentes a arquivos para `public/index.php` e disponibilize as variáveis ao PHP. Mantenha `app/`, `config/`, `tests/`, `storage/` e `vendor/` fora da raiz pública. O `.htaccess` de `public/` já contém a regra de encaminhamento para Apache.

Domínio, hospedagem, configuração real do servidor, `robots.txt`, `sitemap.xml` e preview público em redes sociais continuam pendentes. Não há endereço de demonstração confirmado no projeto.

## Rotas

| Método e caminho | Resposta |
| --- | --- |
| `GET /` | Página inicial e projetos marcados como destaque |
| `GET /projetos` | Catálogo completo ou estado vazio |
| `GET /projetos/{slug}` | Detalhes de um projeto existente |
| Rota, slug ou método não registrado | Página 404 |

Os caminhos respeitam `APP_BASE_PATH`. O catálogo apresenta quatro projetos visíveis; o próprio portfólio permanece oculto até a confirmação da publicação.

## Conteúdo do portfólio

O conteúdo é mantido em arquivos, sem edição pela interface. Os exemplos abaixo demonstram o formato; não representam projetos reais adicionais.

### Perfil

`config/profile.php` concentra apresentação, formação em andamento, estágio, certificações e objetivos. O Model `Profile` fornece os dados ao `HomeController`, e as Views escapam sua saída. O nome da marca está em `config/app.php`.

### Projetos

[config/projects.php](config/projects.php) retorna uma lista. Cada projeto precisa de `id`, `slug`, `title`, `summary` e `description`. Use IDs e slugs únicos; o slug aceita letras minúsculas sem acento e números, separados por hífens únicos.

```php
return [
    [
        'id' => 1,
        'slug' => 'projeto-de-exemplo',
        'title' => 'Projeto de exemplo',
        'summary' => 'Resumo demonstrativo.',
        'description' => 'Descrição demonstrativa, sem representar um projeto real.',
        'technologies' => ['Tecnologia confirmada'],
        'image' => null,
        'github_url' => null,
        'demo_url' => null,
        'featured' => false,
    ],
];
```

`visible` assume `true` quando omitido. `visible => false` oculta o item do catálogo, dos destaques e da consulta por slug, retornando 404 por URL direta. `featured => true` inclui o item visível nos destaques da home. `status` é texto opcional, escapado nos cards e detalhes. `technologies` é uma lista de strings; links e imagem são opcionais. Os links aceitam URLs completas HTTP(S), sem credenciais. Descrições são texto simples, sem interpretação de HTML ou Markdown.

As imagens devem existir em `public/assets/images/`, com caminhos públicos relativos como `assets/images/arquivo.webp`. Os nomes e subpastas aceitam letras ASCII, números, hífens e sublinhados; extensões aceitas: `png`, `jpg`, `jpeg`, `webp`, `gif` e `avif`, em minúsculas. O Model verifica existência e leitura da imagem, obtém suas dimensões e omite a imagem quando necessário, sem placeholder. Cards usam carregamento tardio; a imagem de detalhe é carregada normalmente.

### Habilidades

[config/skills.php](config/skills.php) retorna grupos com `category` e `items`:

```php
return [
    [
        'category' => 'Categoria confirmada',
        'items' => ['Habilidade confirmada', 'Outra habilidade confirmada'],
    ],
];
```

Espaços nas extremidades são removidos e itens vazios são omitidos. A stack do site não é automaticamente apresentada como uma declaração de habilidades pessoais.

### Contatos e currículo

[config/contact.php](config/contact.php) utiliza os campos:

```php
return [
    'github_url' => null,
    'linkedin_url' => null,
    'email' => null,
    'resume' => null,
];
```

Preencha somente dados profissionais aprovados para publicação. As URLs precisam ser completas e usar HTTP(S), sem credenciais. O campo `email` recebe um endereço válido, sem o prefixo `mailto:`.

Para o currículo, disponibilize um PDF aprovado em `public/assets/documents/` e informe um caminho relativo como `assets/documents/curriculo.pdf`. Nomes e subpastas aceitam letras ASCII, números, hífens e sublinhados. Arquivos inexistentes ou fora desse diretório são omitidos; o link de download só aparece quando o caminho é aceito. Nenhum currículo está incluído atualmente.

GitHub e LinkedIn estão preenchidos com os perfis aprovados. O e-mail continua nulo até autorização específica, e nenhum PDF de currículo foi fornecido.

## Verificação e limites

Com as ferramentas instaladas, execute na raiz:

```sh
composer validate --no-check-publish
composer dump-autoload --optimize --strict-psr
php -d error_reporting=E_ALL tests/run.php
find app public config tests -name '*.php' -print0 | xargs -0 -n1 php -l
php -l index.php
```

A suíte existente verifica roteamento, Controllers e Models, renderização, escaping, estados vazios, validação de links e arquivos, metadados e contraste de tokens. Ela cria fixtures temporárias, incluindo arquivos de imagem e documento, e os remove ao concluir. Para preservar integralmente uma cópia de trabalho, execute-a em uma cópia temporária.

Na revisão documental anterior ao povoamento, a suíte foi executada em cópia temporária com PHP 8.4.26 e aprovou **979/979 verificações**. Os **27 arquivos PHP** versionados passaram na checagem de sintaxe. Composer não estava disponível: instalação e validação do autoload não foram repetidas. Também não foram repetidos testes HTTP, Apache, Lighthouse ou inspeção visual no navegador.

[PROGRESS.md](PROGRESS.md) e [PROJECT_CONTEXT.md](PROJECT_CONTEXT.md) preservam o histórico de desenvolvimento e validações anteriores, incluindo o aceite manual da etapa 8 informado pelo proprietário em 18/10/2026. Esses registros são históricos; não representam novas execuções nesta revisão. A publicação continua pendente.

## Imagens e apresentação

`public/assets/images/social-preview.png` e sua fonte SVG são artes para compartilhamento, não capturas de tela. O favicon também é um recurso de identidade visual. Não foram identificadas capturas reais das páginas para incluir aqui.

O conteúdo foi preenchido; ainda é necessário validar a implantação no endereço público. A estrutura MVC, as regras de apresentação e a suíte de verificações já estão implementadas.

## Autoria e repositório

Mantido na conta [Heitorsv001](https://github.com/Heitorsv001). Repositório original: [Heitorsv001/Portifolio](https://github.com/Heitorsv001/Portifolio).
