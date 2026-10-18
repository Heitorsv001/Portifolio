# Portfólio MVC em PHP

Site em PHP organizado no padrão MVC, com as etapas 1 a 3 implementadas: estrutura inicial, layout compartilhado e home de apresentação. Sem framework ou banco de dados; informações pessoais ainda não fornecidas aparecem como placeholders explícitos.

## Requisitos e instalação

- PHP 8.2 ou superior.
- [Composer 2](https://getcomposer.org/download/).

Na raiz do projeto, execute:

```sh
composer install
```

O Composer gera `vendor/autoload.php` com o mapeamento PSR-4 de `App\` para `app/`. Não versione `vendor/`. Nenhuma biblioteca externa é necessária nesta etapa.

## Estrutura

- `app/Controllers`: recebe requisições e coordena a resposta.
- `app/Core`: Controller base e roteador GET.
- `app/Models`: regras e acesso aos dados.
- `app/Views`: layout compartilhado em `layouts/main.php`, header/footer em `partials/` e conteúdo das páginas em `home/` e `errors/`.
- `public/`: ponto de entrada e arquivos públicos (CSS, JavaScript e imagens).
- `config/app.php`: título da aplicação e caminho-base.
- `tests/run.php`: testes isolados de roteamento e renderização, sem framework.
- `storage/logs/`: destino reservado para logs da aplicação.

## Acesso local

Depois de instalar o autoload, execute na raiz do projeto:

```sh
APP_BASE_PATH='' php -S 127.0.0.1:8000 -t public
```

Acesse `http://127.0.0.1:8000/`. O servidor embutido é apenas para desenvolvimento.

O acesso Apache existente em `http://localhost/portifolio/` permanece suportado. O caminho-base padrão é `/portifolio`; pode ser alterado pela variável de ambiente `APP_BASE_PATH`, disponibilizada ao processo PHP. O `.htaccess` encaminha requisições ao front controller e bloqueia acesso direto aos diretórios privados e metadados do projeto. Esse modo exige `mod_rewrite` e permissão de leitura do `.htaccess` pelo Apache.

Em produção, configure a raiz pública do servidor para `public/` e `APP_BASE_PATH` como vazio quando o site estiver na raiz do domínio. O servidor PHP embutido com `-t public` também mantém os arquivos privados fora da raiz pública. Não foi alterada a configuração do servidor nesta etapa.

## Rotas atuais

- `GET /`: página inicial com hero, sobre mim, objetivos e seção informativa de projetos em preparação.
- Rota desconhecida ou método não registrado: página com status HTTP 404.

O Controller solicita a View; o Router apenas encontra e executa a ação. Conteúdo dinâmico e atributos HTML são escapados nas Views. Falhas de inicialização retornam uma mensagem genérica com status 500 e registram detalhes no log do PHP.

## Layout base — etapa 2

O Controller base recebe a configuração da aplicação e fornece nome, título, URL da home e do CSS ao layout. Controllers específicos escolhem a view e podem passar `title` e `currentPage`; a home usa `currentPage = 'home'` para indicar a página atual na navegação. As views de conteúdo não repetem o documento HTML, header, main ou footer. O Controller de erro recebe a mesma configuração, mantendo os links corretos no 404.

Os padrões visuais ficam em `public/assets/css/style.css`: tema escuro com destaque violeta, fontes do sistema (sem download externo), variáveis de cores/espaçamento, títulos fluidos, links e botões `.button` e `.button--secondary`. O container é fluido, header/footer permitem quebra de linha e há um breakpoint em `48rem`. A escolha visual é reversível e aguarda validação do proprietário, seguindo a referência registrada em `PROJECT_CONTEXT.md`.

A navegação inclui somente destinos existentes: início, seções da home, pular para o conteúdo, voltar ao topo e retorno do 404 à home. Há foco visível e indicação `aria-current` na home. Não há JavaScript, menu recolhível, links vazios ou rotas de etapas futuras.

## Página inicial — etapa 3

A view `app/Views/home/index.php` apresenta o título profissional confirmado, Desenvolvedor Full Stack, e sinaliza a ausência do nome definitivo, biografia, formação, interesses e objetivos. Esses placeholders estão na view e devem ser substituídos por texto confirmado pelo proprietário, com escaping caso sejam futuramente fornecidos como dados dinâmicos pelo Controller. O nome da marca e o título do documento continuam configurados em `config/app.php`.

As chamadas do hero levam a `#sobre` e `#projetos`; o menu também oferece `#objetivos`. Os links do header incluem a URL da home com o caminho-base, permitindo voltar às seções a partir de uma página 404. As seções de destino são focáveis por navegação de fragmentos (`tabindex="-1"`), sem acrescentar paradas ao percurso normal de Tab.

A seção `#projetos` contém apenas um aviso de preparação, para que a chamada tenha um destino válido. Catálogo, cards, dados, Model, Controller e rota `/projetos` serão implementados na etapa 4. Habilidades, contatos e currículo também permanecem nas etapas correspondentes.

## Verificações

```sh
composer validate --no-check-publish
composer dump-autoload --optimize --strict-psr
php tests/run.php
find app public config tests -name '*.php' -print0 | xargs -0 -n1 php -l
php -l index.php
```

Os testes isolados carregam as classes diretamente: verificam rotas, prefixo da instalação, métodos HTTP, escaping, layout único, placeholders, chamadas da home, identificadores únicos, destinos das âncoras locais e de retorno à home e renderização de 404, sem substituir a validação do autoload Composer ou do servidor web. Execute `php tests/run.php`; não há framework de testes.

Com o servidor local em execução, confira a integração:

```sh
curl -i http://127.0.0.1:8000/
curl -i http://127.0.0.1:8000/rota-inexistente
curl -i http://127.0.0.1:8000/assets/css/style.css
```

Os resultados esperados são, respectivamente, status 200 com a página inicial, status 404 e status 200 com o CSS. No navegador, confira as seções em telas pequenas e grandes, o carregamento do estilo e o console. Use Tab e Enter para testar o link de salto, o menu, as chamadas do hero e o retorno às seções a partir de uma página 404; confira foco visível e ausência de rolagem horizontal.

## Validação da etapa 1 — 18/10/2026

Patch `portfolio-etapa1.patch` aplicado sem conflitos. Composer instalado temporariamente em `/tmp/portfolio-composer.phar`, com integridade do instalador conferida pela assinatura SHA-384 oficial; `composer.lock` e `vendor/autoload.php` gerados.

- Composer install, validate e dump-autoload otimizado com `--strict-psr`: aprovados. Único aviso de validação: licença ainda não definida pelo proprietário.
- `php tests/run.php`: 43/43 verificações aprovadas.
- Sintaxe dos 10 arquivos PHP: sem erros.
- Integração real com servidor PHP e autoload Composer: home 200, rota inexistente 404 e CSS 200.
- Na verificação inicial do Apache em `http://localhost/portifolio/`, CSS retornou 200 e `config/app.php` retornou 403, mas o PHP era entregue como texto. Após receber as instruções para habilitar PHP-FPM, o proprietário confirmou que a correção funcionou. Essa confirmação não substitui uma nova verificação automatizada das rotas no Apache.
- Renderização visual, responsividade e console não puderam ser inspecionados: nenhum navegador está disponível na sessão. A view de teste não contém JavaScript nem elementos interativos para validar por teclado.

Os critérios funcionais da etapa 1 foram confirmados no servidor PHP documentado acima. O problema de execução PHP no Apache foi resolvido conforme confirmação do proprietário; a revisão visual completa permanece pendente. Nenhuma tarefa da etapa 2 foi iniciada.

## Validação da etapa 2 — 18/10/2026

- `php tests/run.php`: **141/141** verificações aprovadas, incluindo layout e navegação na raiz, em `/portifolio` e em subdiretório aninhado.
- Sintaxe dos **13 arquivos PHP** em `app/`, `public/`, `config/`, `tests/` e `index.php`: sem erros; `git diff --check` aprovado.
- Apache em `http://localhost/portifolio/`: home **200** com HTML executado, rota inexistente **404**, CSS **200**, configuração e partial privados **403**.
- Servidor PHP com `APP_BASE_PATH=''` e raiz `public/`: home **200**, rota inexistente **404** e CSS **200**. Servidor temporário encerrado após a validação.
- Corpos das respostas conferidos nos dois servidores: documento e landmarks únicos, destinos de links/âncoras existentes, página ativa correta e CSS idêntico ao arquivo atualizado.
- Não foi possível validar renderização visual, redimensionamento, zoom, console ou interação real com teclado: o inventário de navegadores/aplicativos está vazio nesta sessão. Esses testes manuais permanecem pendentes; a base inclui os estilos e a semântica necessários.

Ao final da etapa 2, a etapa 3 ainda não havia sido iniciada.

## Validação da etapa 3 — 18/10/2026

- `php tests/run.php`: **186/186** verificações aprovadas; suite anterior com **141/141** também aprovada antes das alterações.
- Sintaxe dos **13 arquivos PHP**: sem erros; `git diff --check` aprovado.
- Apache com `/portifolio` e servidor PHP com `APP_BASE_PATH=''`: home **200**, rota inexistente **404**, CSS **200**, `/projetos` **404** (rota da etapa 4 ainda não implementada).
- Apache: acesso direto à view em `/portifolio/app/Views/home/index.php` retorna **403**.
- HTML das respostas conferido: layout e h1 únicos, hierarquia de títulos, IDs e referências `aria-labelledby` válidos, âncoras locais existentes e links do 404 apontando às seções da home. CSS servido idêntico ao arquivo atualizado.
- Servidor temporário encerrado após a validação; nenhuma mudança no Apache ou nas dependências.
- Inspeção visual, responsividade, console, zoom e teclado real continuam pendentes: nenhum aplicativo/navegador disponível na sessão. A inspeção de HTML não substitui esses testes.

Etapa 4 não iniciada. Nome definitivo, biografia, formação, interesses, objetivos, projetos, habilidades e contatos continuam pendentes de confirmação do proprietário. Consulte `PROGRESS.md` para a passagem de contexto atual.
