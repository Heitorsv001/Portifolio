# Portfólio MVC em PHP

Site em PHP organizado no padrão MVC, com as etapas 1 a 7 implementadas e as melhorias da etapa 8 aplicadas (Lighthouse validado; aceite manual de zoom/teclado pendente). Inclui layout compartilhado, home, catálogo, detalhes dos projetos, habilidades, contato e metadados por página. Sem framework ou banco de dados; informações pessoais ainda não fornecidas aparecem como placeholders explícitos. Projetos, habilidades e contatos permanecem sem dados pessoais até a confirmação do proprietário.

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
- `app/Views`: layout compartilhado em `layouts/main.php`, header/footer e cards em `partials/` e conteúdo das páginas em `home/`, `projects/` e `errors/`.
- `public/`: ponto de entrada e arquivos públicos (CSS, JavaScript e imagens).
- `config/app.php`: nome da aplicação, caminho-base e origem pública opcional para URLs absolutas.
- `config/projects.php`: dados confirmados dos projetos; fonte única do catálogo e dos destaques.
- `config/skills.php`: habilidades confirmadas, agrupadas por categoria e apresentadas na home.
- `config/contact.php`: GitHub, LinkedIn, e-mail profissional e caminho do currículo opcional.
- `public/assets/documents/`: PDFs públicos, incluindo o currículo quando fornecido; inicia sem documentos.
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

- `GET /`: página inicial com hero, sobre mim, objetivos, projetos em destaque, habilidades e contato.
- `GET /projetos`: catálogo completo, com estado vazio enquanto não houver projetos confirmados.
- `GET /projetos/{slug}`: página individual de um projeto cadastrado; slug inválido ou inexistente retorna 404.
- Rota desconhecida ou método não registrado: página com status HTTP 404.

O Controller solicita a View; o Router apenas encontra e executa a ação. Conteúdo dinâmico e atributos HTML são escapados nas Views. Falhas de inicialização retornam uma mensagem genérica com status 500 e registram detalhes no log do PHP.

## Layout base — etapa 2

O Controller base recebe a configuração da aplicação e fornece nome, título, URL da home e do CSS ao layout. Controllers específicos escolhem a view e podem passar `title` e `currentPage`; a home usa `currentPage = 'home'` para indicar a página atual na navegação. As views de conteúdo não repetem o documento HTML, header, main ou footer. O Controller de erro recebe a mesma configuração, mantendo os links corretos no 404.

Os padrões visuais ficam em `public/assets/css/style.css`: tema escuro com destaque violeta, fontes do sistema (sem download externo), variáveis de cores/espaçamento, títulos fluidos, links e botões `.button` e `.button--secondary`. O container é fluido, header/footer permitem quebra de linha e há um breakpoint em `48rem`. A escolha visual é reversível e aguarda validação do proprietário, seguindo a referência registrada em `PROJECT_CONTEXT.md`.

A navegação inclui somente destinos existentes: início, seções da home, catálogo, pular para o conteúdo, voltar ao topo e retorno do 404 à home. Há foco visível e indicação `aria-current` na home ou no catálogo. Não há JavaScript, menu recolhível, links vazios ou rotas de etapas futuras.

## Página inicial — etapa 3

A view `app/Views/home/index.php` apresenta o título profissional confirmado, Desenvolvedor Full Stack, e sinaliza a ausência do nome definitivo, biografia, formação, interesses e objetivos. Esses placeholders estão na view e devem ser substituídos por texto confirmado pelo proprietário, com escaping caso sejam futuramente fornecidos como dados dinâmicos pelo Controller. O nome da marca e o título do documento continuam configurados em `config/app.php`.

As chamadas do hero levam a `#sobre` e `#projetos`; o menu também oferece `#objetivos`. Os links do header incluem a URL da home com o caminho-base, permitindo voltar às seções a partir de uma página 404. As seções de destino são focáveis por navegação de fragmentos (`tabindex="-1"`), sem acrescentar paradas ao percurso normal de Tab.

A seção `#projetos`, inicialmente informativa na etapa 3, agora recebe os destaques e a chamada para o catálogo da etapa 4. As seções `#habilidades` e `#contato` foram acrescentadas nas etapas 6 e 7, respectivamente.

## Catálogo e cadastro de projetos — etapa 4

O Model `App\Models\Project` lê `config/projects.php` e oferece `all()` e `featured()`. `ProjectController` envia todos os itens à listagem; `HomeController` envia apenas os marcados com `featured => true`. As duas views reutilizam `partials/project-list.php`. Views não leem configuração e Controllers não geram HTML. A ordem dos itens no arquivo é preservada.

Para cadastrar um projeto confirmado, adicione um array dentro do `return [...]` de `config/projects.php`. Exemplo **fictício, apenas para demonstrar o formato**; não representa um projeto do proprietário:

```php
[
    'id' => 1,
    'slug' => 'exemplo-de-projeto',
    'title' => 'Exemplo de projeto',
    'summary' => 'Resumo curto do projeto.',
    'description' => 'Descrição completa apresentada na página individual.',
    'technologies' => ['PHP', 'HTML', 'CSS'],
    'image' => null,
    'github_url' => null,
    'demo_url' => null,
    'featured' => true,
],
```

Use IDs e slugs únicos; slugs com segmentos de letras minúsculas sem acentos e/ou números separados por um único hífen, sem hífen inicial ou final (ex.: `meu-projeto-2`). `id`, `slug`, `title`, `summary` e `description` são obrigatórios. `technologies` aceita uma lista de strings (vazia se não informada); `featured` é booleano e assume `false` quando omitido. Os links e a imagem podem ser omitidos ou receber `null`. Não é necessário editar Views para incluir itens ou alterar destaques.

As URLs de repositório e demonstração devem ser públicas e completas, com `http://` ou `https://`, sem credenciais; valores vazios, inválidos ou com outros esquemas são omitidos. Links abrem na mesma aba e têm nomes acessíveis que identificam o projeto. Todo texto e atributo dinâmico é escapado.

Imagens são locais, sob `public/assets/images/`. Informe o caminho relativo público, por exemplo `assets/images/meu-projeto.webp`, sem barra inicial. São aceitas extensões minúsculas `png`, `jpg`, `jpeg`, `webp`, `gif` e `avif`, com nomes e subpastas contendo letras, números, hífens ou sublinhados. Imagem ausente, inexistente, ilegível como imagem ou fora desse padrão recebe fallback visual, sem request quebrado. O Model obtém `image_width`/`image_height` do arquivo: não é necessário cadastrá-los. As Views usam essas dimensões para reservar a proporção correta; cards mantêm recorte 16:9, carregamento tardio e grid fluido. Otimize os arquivos reais antes de publicá-los; não há processamento de imagens em upload ou biblioteca extra.

O catálogo vazio e a home sem destaques possuem avisos próprios. O arquivo real continua vazio até o proprietário confirmar projetos.

## Páginas individuais — etapa 5

Cada título de card no catálogo e nos destaques aponta para `/projetos/{slug}`, respeitando o caminho-base. A URL pode ser aberta diretamente ou compartilhada; não depende de navegação anterior. Cadastrar um item válido em `config/projects.php` disponibiliza seu detalhe automaticamente.

O Router reconhece parâmetros de um segmento como `{slug}`, mantém a prioridade das rotas fixas e decodifica os parâmetros uma única vez. `ProjectController::show()` consulta `Project::findBySlug()`; slugs inválidos ou sem correspondência usam o Controller/view de erro existente com status 404. A validação de slug permanece no Model, sem regras de projetos no Router.

A view `projects/show.php` apresenta título específico no documento, h1, resumo, descrição completa e, quando disponíveis, imagem, tecnologias, repositório e demonstração. O link “Voltar aos projetos” retorna ao catálogo. Tecnologias e links externos são partials compartilhadas com os cards. No detalhe, o menu não atribui `aria-current="page"` à home ou ao catálogo.

Descrições são texto simples, escapado; quebras de linha são preservadas por CSS, sem interpretar HTML ou Markdown. Imagens e links reutilizam a validação da etapa 4. Imagem ausente é omitida no detalhe; seções vazias de tecnologias e links também são omitidas. Sem JavaScript ou novas dependências.

## Habilidades e tecnologias — etapa 6

A seção `/#habilidades` utiliza `partials/skills.php`, com heading próprio, categorias em h3 e listas de conhecimentos. O menu aponta para essa seção também a partir do catálogo, detalhes e 404, respeitando o caminho-base. Não há rota separada de habilidades.

O Model `App\Models\Skill` lê `config/skills.php` e fornece `all()` ao `HomeController`, que envia `skillGroups` à View. Views não leem o arquivo de dados. O arquivo real retorna `[]` e a home sinaliza “Habilidades a confirmar”; nenhuma tecnologia do projeto, referência visual ou fixture é inferida como conhecimento pessoal.

Após a confirmação do proprietário, inclua grupos no `return [...]` do arquivo. Este exemplo demonstra apenas o formato:

```php
[
    'category' => 'Categoria confirmada',
    'items' => ['Habilidade confirmada', 'Outra habilidade confirmada'],
],
```

Cada grupo deve conter `category` como string e `items` como lista de strings. Os nomes das categorias são livres: podem organizar linguagens, back-end, front-end, banco de dados ou ferramentas conforme os conhecimentos confirmados. A ordem do arquivo é preservada; espaços nas extremidades são removidos e itens em branco ou categorias sem nome/itens são omitidos. Se nenhum grupo preenchido restar, aparece o estado vazio. Basta editar esse arquivo para incluir categorias e habilidades, sem alterar Controller, View ou CSS.

Categorias e habilidades são texto simples escapado, sem HTML, links, ícones externos ou níveis de proficiência. O grid usa os tokens visuais existentes e adapta as colunas ao espaço disponível. Não há novas dependências ou JavaScript.

## Contato e redes profissionais — etapa 7

A seção `/#contato`, após as habilidades, usa `partials/contact.php` e é acessível pelo menu da home, catálogo, detalhes e 404. `App\Models\Contact` lê `config/contact.php`, normaliza os valores e oferece `all()`; o HomeController envia os dados à View. Não há nova rota, formulário ou envio de e-mail pelo servidor.

O arquivo real mantém os quatro campos como `null` e a home mostra “Contatos a confirmar”. Preencha apenas os dados confirmados pelo proprietário. Campos podem ser omitidos ou continuar nulos; cada link é habilitado individualmente. Exemplo de estrutura, **sem contatos reais**:

```php
return [
    'github_url' => null,
    'linkedin_url' => null,
    'email' => null,
    'resume' => null,
];
```

- `github_url` e `linkedin_url`: URLs completas HTTP(S) dos perfis confirmados, sem credenciais. URLs inválidas ou com outros esquemas são omitidas. Links abrem na mesma aba, seguindo o padrão dos projetos; não usam `target="_blank"` e não precisam de `rel="noopener"`.
- `email`: endereço profissional, sem prefixo `mailto:`, assunto ou parâmetros. O Model valida o endereço e remove espaços nas extremidades. A View escapa o texto e codifica os caracteres especiais na URI `mailto:`, preservando o endereço como destinatário único. O link abre o aplicativo de e-mail configurado pelo visitante; não envia mensagens automaticamente.
- `resume`: caminho público relativo, por exemplo `assets/documents/curriculo.pdf`, somente após disponibilizar o PDF aprovado em `public/assets/documents/`. Nomes/subpastas aceitam letras ASCII, números, hífens e sublinhados; a extensão deve ser `.pdf`. O arquivo precisa existir e seu caminho real deve permanecer nesse diretório. Caminhos externos, travessia, links simbólicos para fora do diretório e arquivos ausentes são omitidos.

O currículo usa um link local com atributo `download`, nome visível “Baixar currículo (PDF)” e caminho-base da instalação. Para desabilitá-lo, defina `resume` como `null` ou remova o arquivo. Nenhum currículo foi criado ou publicado nesta etapa. Texto e atributos são escapados; sem JavaScript, rastreamento ou novas dependências.

## Responsividade, acessibilidade e SEO — etapa 8

O CSS mantém navegação visível com quebra de linha, ocupa a linha inteira do header nas telas pequenas e permite redução de colunas e quebra de textos longos. Catálogo/detalhe usam colunas com mínimo zero; links dos títulos dos cards têm altura mínima de `2.75rem`, seguindo os demais links de navegação. O link de salto respeita a largura disponível. Foco visível, ordem natural de Tab, destinos focáveis de fragmentos, HTML semântico e headings existentes foram preservados. Listas sem marcador visual declaram `role="list"` para manter sua semântica, inclusive no comportamento documentado do Safari para [`list-style: none`](https://developer.mozilla.org/en-US/docs/Web/CSS/Reference/Properties/list-style#accessibility).

Os testes calculam contraste dos pares de tokens usados em textos/botões e foco. O menor contraste de texto/botão é **7,61:1**; o menor contraste de foco sobre os fundos é **12,28:1**. O cálculo não substitui uma auditoria visual completa; o critério de texto normal usado é o da [WCAG 2.2, contraste mínimo](https://www.w3.org/WAI/WCAG22/Understanding/contrast-minimum.html).

Cada Controller informa título, descrição e caminho público da página. O detalhe usa o resumo do projeto como descrição; espaços/quebras são normalizados, e valores são escapados em atributos. O layout renderiza uma descrição única e metadados [Open Graph](https://ogp.me/) de título, descrição, tipo `website`, nome do site e locale `pt_BR`. O 404 mantém status HTTP 404, recebe `noindex, follow` e não publica canonical ou Open Graph de uma página válida.

Quando o domínio estiver confirmado, disponibilize ao processo PHP:

```sh
APP_ORIGIN='https://SEU-DOMINIO' APP_BASE_PATH='' php -S 127.0.0.1:8000 -t public
```

Substitua `https://SEU-DOMINIO` pela origem pública real, contendo apenas esquema HTTP(S), host e porta opcional. Não inclua caminho, query string, fragmento ou credenciais. Subdiretórios continuam em `APP_BASE_PATH` (ex.: `/portifolio`). A origem não é inferida de cabeçalhos da requisição.

Com origem válida, o layout emite `rel="canonical"` e `og:url` absolutos, calculados a partir da rota conhecida, sem query string e sem variações de codificação/barra final. Essa escolha segue a orientação de [URLs canônicas absolutas do Google Search Central](https://developers.google.com/search/docs/crawling-indexing/consolidate-duplicate-urls). A mesma origem compõe `og:image`, com dimensões, MIME e texto alternativo. O detalhe usa a imagem do projeto; home, catálogo e projetos sem imagem usam `assets/images/social-preview.png` (1200×630, 25.149 bytes).

`APP_ORIGIN` ausente ou inválida omite canonical, `og:url` e `og:image`, mantendo os metadados textuais. Nenhum domínio foi inventado; o preview completo em redes depende dessa configuração e da publicação. `robots.txt` e `sitemap.xml` continuam pendentes da definição do domínio, conforme o contexto do projeto.

A imagem padrão contém somente o título profissional confirmado e o tema do portfólio, sem biografia ou projetos inventados. A fonte editável é `public/assets/images/social-preview.svg`; o PNG foi gerado e inspecionado visualmente. Para regenerá-lo após editar o SVG, use opcionalmente ImageMagick no ambiente de manutenção (não é dependência de execução PHP):

```sh
magick -background none public/assets/images/social-preview.svg -strip PNG24:public/assets/images/social-preview.png
```

O CSS tem 9.905 bytes; o site não acrescenta JavaScript, fontes remotas ou bibliotecas nesta etapa. Cards continuam com lazy loading, o detalhe mantém imagem sem lazy loading e os atributos de dimensão refletem o arquivo real. O favicon local `assets/images/favicon.svg` reutiliza o símbolo da marca e a paleta existente; seu endereço respeita o caminho-base, evitando a busca automática por um ícone inexistente na raiz do servidor.

Auditorias locais com Lighthouse 13.5.0 e Chrome headless 155 obtiveram **100 em desempenho, acessibilidade, boas práticas e SEO** na home mobile/desktop, catálogo vazio e estados preenchidos de teste. Textos longos, categorias, e-mail e imagens de proporções diferentes foram usados apenas em uma cópia temporária, sem alterar os dados reais. Capturas produzidas pelas auditorias permitiram inspeção estática nas larguras de 320, 375, 768 e 1280 CSS px. Resultados detalhados e limites estão em `PROGRESS.md`; medições locais de laboratório não comprovam Core Web Vitals de usuários reais.

**Aceite manual pendente:** a ferramenta de interação continua sem navegador conectado. Conferir zoom real de 200%/400%, rolagem, foco e Tab/Shift+Tab/Enter, incluindo salto ao conteúdo, âncoras e retorno do 404. A revisão de [reflow da WCAG](https://www.w3.org/WAI/WCAG22/Understanding/reflow.html) e teclado deve ser concluída antes de marcar a etapa 8 como validada; capturas estáticas e Lighthouse não substituem essa interação. Conferir também o preview real no LinkedIn após configurar o domínio.

## Verificações

```sh
composer validate --no-check-publish
composer dump-autoload --optimize --strict-psr
php tests/run.php
php -d error_reporting=E_ALL tests/run.php
find app public config tests -name '*.php' -print0 | xargs -0 -n1 php -l
php -l index.php
```

Para repetir a auditoria opcional, use Chrome e Node compatível com o [Lighthouse CLI](https://github.com/GoogleChrome/lighthouse#using-the-node-cli) (versão usada: Node 24.19.0). A instalação abaixo fica em `/tmp`, sem dependências de execução novas na aplicação; o servidor local precisa estar em execução:

```sh
npm install --prefix /tmp/portfolio-lighthouse --no-save --ignore-scripts lighthouse@13.5.0
node /tmp/portfolio-lighthouse/node_modules/lighthouse/cli/index.js http://localhost/portifolio/ --chrome-flags="--headless" --only-categories=performance,accessibility,best-practices,seo --output=json --output=html --output-path=/tmp/portfolio-lighthouse/mobile
node /tmp/portfolio-lighthouse/node_modules/lighthouse/cli/index.js http://localhost/portifolio/ --chrome-flags="--headless" --preset=desktop --only-categories=performance,accessibility,best-practices,seo --output=json --output=html --output-path=/tmp/portfolio-lighthouse/desktop
```

Para larguras específicas, acrescente `--screenEmulation.width=320` (ou 375, 768, 1280), `--screenEmulation.height=900` e `--screenEmulation.deviceScaleFactor=1`; nesta validação, 768 e 1280 usaram o preset desktop. Não publique fixtures para repetir os estados preenchidos: use uma cópia temporária isolada. Relatórios em `/tmp` podem desaparecer entre sessões.

Os testes isolados carregam as classes diretamente: verificam rotas fixas e parametrizadas, prefixo da instalação, métodos HTTP, escaping, layout único, placeholders, chamadas da home, identificadores únicos, destinos das âncoras locais e de retorno à home, catálogo, detalhes e renderização de 404. Dados fictícios em arquivos temporários validam inclusão de itens, seleção de destaques, busca/validação por slug, campos opcionais, imagens locais e rejeição de URLs inseguras. Habilidades são testadas com categorias preenchidas/vazias, ordenação, atualização apenas pelo arquivo, escaping e navegação nos diferentes caminhos-base. Contato cobre valores ausentes/parciais, URLs/e-mails inválidos, escaping, codificação de `mailto:`, PDF local existente/ausente e download com caminho-base. SEO cobre descrições, Open Graph, origem válida/inválida, canonical, noindex no 404, dimensões e fallback de imagens, além de contrastes e invariantes semânticas. As fixtures são removidas ao final, sem alterar `config/projects.php`, `config/skills.php` ou `config/contact.php`. Isso não substitui a validação do autoload Composer ou do servidor web. Execute `php tests/run.php`; não há framework de testes.

Com o servidor local em execução, confira a integração:

```sh
curl -i http://127.0.0.1:8000/
curl -i http://127.0.0.1:8000/projetos
curl -i http://127.0.0.1:8000/rota-inexistente
curl -i http://127.0.0.1:8000/assets/css/style.css
```

Os resultados esperados são, respectivamente, status 200 com a página inicial, 200 com o catálogo, 404 e 200 com o CSS. No navegador, confira as seções e cards em telas pequenas e grandes, o carregamento do estilo e o console. Use Tab e Enter para testar o link de salto, o menu, as chamadas do hero, o acesso ao catálogo, os links dos cards quando cadastrados e o retorno às seções a partir de uma página 404; confira foco visível e ausência de rolagem horizontal.

Após cadastrar um projeto confirmado, confira `GET /projetos/SEU-SLUG` com status 200, título/descrição corretos, imagem e links disponíveis, navegação pelo título do card e retorno ao catálogo. Um slug desconhecido deve responder 404. Não adicione projetos fictícios ao arquivo real para publicação.

Após cadastrar habilidades confirmadas, confira as categorias e listas na home em mobile/desktop, inclusive com textos longos e zoom. Use Tab/Enter no link “Habilidades” do menu e verifique o destino/foco da seção a partir da home, catálogo, detalhe e 404.

Após informar os contatos confirmados, use Tab/Enter no menu “Contato” e nos links da seção, confira os perfis corretos, o destinatário no aplicativo de e-mail e o download/leitura do currículo quando habilitado. Confira também estados parciais, endereço longo, mobile/desktop e console. Sem dados confirmados, não é possível validar a disponibilidade dos perfis reais nem o conteúdo do currículo final.

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

Ao final da etapa 3, a etapa 4 ainda não havia sido iniciada. Nome definitivo, biografia, formação, interesses, objetivos, projetos, habilidades e contatos continuam pendentes de confirmação do proprietário. Consulte `PROGRESS.md` para a passagem de contexto atual.

## Validação da etapa 4 — 18/10/2026

- `php tests/run.php`: **320/320** verificações aprovadas; suite anterior com **186/186** aprovada antes das mudanças.
- Sintaxe dos **18 arquivos PHP**: sem erros; `git diff --check` aprovado.
- Apache com `/portifolio` e servidor PHP com `APP_BASE_PATH=''`: home e catálogo **200**, catálogo com barra final/query **200**, rota inexistente e detalhe de projeto **404**, POST ao catálogo **404**, CSS **200** e idêntico ao arquivo local.
- Apache: acesso direto a `config/projects.php` e à partial de cards **403**.
- HTML servido: layout e h1 únicos, IDs e referências de headings válidos, links/âncoras com destinos existentes e página ativa correta. Autoload Composer existente funciona com as novas classes, sem novas dependências.
- Servidor temporário encerrado e sem erros de aplicação; nenhuma configuração do Apache ou permissão foi alterada.
- Navegador indisponível (`apps: []`, `browsers: []`): inspeção visual, console, zoom e teclado real permanecem pendentes. Cards preenchidos foram validados com fixtures isoladas, sem publicar projetos fictícios.

Ao final da etapa 4, a etapa 5 ainda não havia sido iniciada. Dados reais dos projetos e confirmação visual permaneciam pendentes.

## Validação da etapa 5 — 18/10/2026

- Suíte anterior: **320/320**; após a implementação: **589/589**, inclusive com `error_reporting=E_ALL`.
- Sintaxe dos **21 arquivos PHP** sem erros; `git diff --check` aprovado.
- Fixtures na raiz, `/portifolio` e subdiretório aninhado: detalhes corretos com/sem opcionais, links de cards, retorno ao catálogo, escaping, slugs inválidos/desconhecidos e status 404. Router preserva rotas fixas, métodos e prefixo; parâmetros decodificados uma vez.
- Apache real, com catálogo vazio: home/catálogo **200**, slugs desconhecidos/inválidos e segmento extra **404**, POST ao detalhe **404**, CSS **200** idêntico ao local; configuração e view do detalhe privadas **403**.
- Cópia temporária isolada com fixtures e servidor PHP com raiz `public/`: fluxo card → detalhe → catálogo aprovado com autoload Composer. Detalhes completos, mínimos e com opcionais inválidos **200**; imagem **200**; query/barra final e slug válido codificado **200**; slugs desconhecidos/inválidos e POST **404**. HTML, escaping, headings, IDs, âncoras e CSS conferidos.
- Cópia temporária removida e servidor encerrado, sem erros de aplicação. `config/projects.php` real permaneceu vazio; alterações da etapa 4 foram preservadas.
- Navegador indisponível (`apps: []`, `browsers: []`): inspeção visual, zoom, console e teclado real pendentes. Não houve implementação da etapa 6.
