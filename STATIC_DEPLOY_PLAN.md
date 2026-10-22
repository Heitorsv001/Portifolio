# Plano de exportação estática do portfólio PHP MVC

Análise em 22/10/2026. Este documento propõe a implementação; nenhum exportador, workflow ou artefato de publicação foi criado nesta etapa. O código da aplicação permanece intacto. Deploy, commits e pushes não foram executados.

> Atualização de implementação em 22/10/2026: **etapas 1–5 concluídas e validadas localmente** em tarefa posterior à análise acima. Comandos e comportamento efetivo estão em `README.md`; resultados em `STATIC_EXPORT_VALIDATION.md`. O coletor copia apenas assets referenciados (sete atualmente), em vez de todos os recursos públicos. Foram acrescentados dois arquivos de suporte em `scripts/` e um teste HTTP/visual em `tests/static-browser.js`. DOM continua restrito às ferramentas e foi carregado temporariamente no ambiente local. O gerador de produção exige HTTPS; preview HTTP é feito servindo o artefato. As demais seções preservam o planejamento original, inclusive seus resultados anteriores. Não foram iniciadas configuração de hospedagem, publicação ou automação (etapas 6–7).

## 1. Viabilidade e recomendação

**A exportação estática é viável e é a opção recomendada para este portfólio.** Manter PHP MVC, Models e configurações como fonte de desenvolvimento e executar o PHP durante a geração. Publicar somente os HTMLs resultantes e os assets existentes.

O site atual não depende de banco de dados, sessão, autenticação, API ou processamento de formulário. Os Controllers transformam dados locais em HTML; tema, navegação mobile e partículas já funcionam com JavaScript no navegador. A hospedagem não precisará executar PHP nem Composer.

O resultado será um site com várias páginas reais, acessíveis diretamente e sem JavaScript. Não é necessário introduzir React, Vue, roteamento no navegador, um framework de geração estática ou duplicar o catálogo em JSON.

As adaptações principais são pequenas, mas precisam de validação: geração de URLs com barra final, prefixo para sites de projeto no GitHub Pages, origem pública para SEO, exclusão de projetos ocultos em reconstruções e entrega de 404 com status correto.

### Comparação com uma reescrita completa

| Critério | Exportar o PHP MVC existente | Reescrever em HTML e JavaScript |
| --- | --- | --- |
| Esforço inicial | Exportador, adaptação central de URLs e validação do artefato. | Recriar páginas, compartilhamento de layout, catálogo, detalhes e metadados. |
| Fonte do conteúdo | Continua em `config/*.php`, consultada pelos Models atuais. | Precisaria migrar ou duplicar dados e definir nova forma de manter páginas. |
| Manutenção | Uma correção na view aparece em todos os HTMLs na reconstrução. | HTML manual duplica markup; templates novos recriam infraestrutura já existente. |
| Funcionalidades | Reaproveita integralmente CSS, scripts, HTML semântico e escaping. | Exige regressão mais ampla; recriar conteúdo via JS adicionaria dependência desnecessária no navegador. |
| SEO e acesso direto | Metadados e conteúdo completos em cada HTML. | HTML manual pode preservá-los, mas aumenta manutenção; renderização via JS exigiria cuidados extras. |
| Desenvolvimento | PHP 8.2+ e autoload continuam necessários para gerar. | Pode dispensar PHP, mas deixa de atender ao objetivo de preservá-lo como ambiente principal. |
| Risco | Concentrado no build e na entrega de URLs/404. | Maior área de mudanças e possibilidade de divergência visual e de conteúdo. |

Estimativa de planejamento para a exportação: aproximadamente **2–3 dias de trabalho**, incluindo testes dos dois destinos e automação básica, dependendo do ambiente e da revisão visual. Uma reescrita demandaria esforço maior sem benefício funcional identificado. Não é orçamento nem prazo validado por implementação.

## 2. Evidências no repositório

Foram examinados `AGENTS.md`, `README.md`, `PROJECT_CONTEXT.md`, registros pertinentes de `PROGRESS.md`, o relatório de validação do redesign e os planos existentes `DEPLOY_PLAN.md` e `INFINITYFREE_COMPATIBILITY_PLAN.md`. Estes dois últimos permanecem preservados e descrevem alternativas de hospedagem PHP; a exportação estática passa a ser detalhada aqui.

### Entrada, Router e Controllers

- `public/index.php` inicializa o autoload Composer, carrega `config/app.php`, registra três padrões GET e despacha pelo `Router`. Captura falhas e retorna 500.
- `index.php` na raiz apenas encaminha à entrada pública. A reescrita Apache da raiz contém `/portifolio` fixo; ela não deve acompanhar a saída estática.
- `app/Core/Router.php` trata prefixo, query string e barra final; despacha `/`, `/projetos` e `/projetos/{slug}`. Não possui inventário público de rotas nem exportador.
- `HomeController::index()` renderiza perfil, projetos, habilidades e contatos. `ProjectController::index()` renderiza o catálogo e `show()` consulta o slug. `ErrorController::notFound()` usa a view existente e define 404.
- O Router aceita somente GET. HEAD e outros métodos na hospedagem estática terão tratamento do provedor, que pode diferir do PHP. Não há operação de escrita a preservar; registrar essa diferença no aceite, sem criar Functions para reproduzir artificialmente o Router.

### Models e configurações

- `Project`, `Profile`, `Skill` e `Contact` leem as quatro configurações de conteúdo; `app.php` é a quinta configuração, carregada pela entrada. Não há consulta remota para renderizar o portfólio.
- `Project` filtra `visible` antes de disponibilizar `all()`, `featured()` e `findBySlug()`. Campo ausente ou nulo assume `true`; nos demais casos, somente o valor booleano `true` passa pelo filtro.
- `findBySlug()` aceita letras minúsculas ASCII e números separados por hífens. O exportador deve preservar essa regra, validar unicidade e falhar em colisões; não renomear slugs automaticamente.
- `Contact` valida redes externas, e-mail e o PDF local por `realpath()`. `Project` valida imagens e lê suas dimensões dentro de `public/`.
- `config/app.php` lê `APP_BASE_PATH` e `APP_ORIGIN`, sem biblioteca `.env`. O fallback local é `/portifolio`; a origem é nula quando não definida.

Rotas concretas públicas atuais, sem o prefixo de hospedagem:

| Rota MVC atual | HTML a gerar | URL estática preferida |
| --- | --- | --- |
| `/` | `index.html` | `/` |
| `/projetos` | `projetos/index.html` | `/projetos/` |
| `/projetos/gerenciador-de-permanencias` | `projetos/gerenciador-de-permanencias/index.html` | `/projetos/gerenciador-de-permanencias/` |
| `/projetos/crowns` | `projetos/crowns/index.html` | `/projetos/crowns/` |
| `/projetos/festividades-de-fim-de-ano` | `projetos/festividades-de-fim-de-ano/index.html` | `/projetos/festividades-de-fim-de-ano/` |
| `/projetos/noticias` | `projetos/noticias/index.html` | `/projetos/noticias/` |

São **seis páginas públicas e uma página de erro**. `portfolio-mvc-php` está com `visible => false` e não deve gerar diretório, HTML, card, link ou entrada de sitemap. A publicação não autoriza habilitar esse projeto automaticamente.

### Views, layout, caminhos e SEO

- `app/Views/layouts/main.php` concentra documento HTML, metadados, favicon, scripts e CSS. Header, rodapé, cards, tecnologias, habilidades e contato usam partials.
- As views usam `$basePath`, `$homeUrl` e `$projectsUrl`. A exceção relevante para a barra final é `partials/project-list.php`, que monta o detalhe concatenando `$projectsUrl . '/' . rawurlencode($project['slug'])`.
- `app/Core/Controller.php` concentra URLs de assets, canonical e Open Graph. CSS e scripts usam `?v=` com os primeiros 12 caracteres do SHA-256 do arquivo; esse mecanismo já serve para a exportação.
- `title` e descrição vêm dos Controllers. A descrição é normalizada e escapada pelo layout. Canonical e `og:url` dependem de uma origem válida e de `pagePath`; atualmente catálogo e detalhes são emitidos sem barra final.
- A imagem de compartilhamento é a do projeto quando válida ou `assets/images/social-preview.png` como fallback. Todos os projetos atuais têm imagem nula. A 404 já contém `noindex, follow` e omite canonical e Open Graph.
- `robots.txt` e `sitemap.xml` ainda não existem. Devem ser gerados a partir do mesmo conjunto de páginas públicas e da origem definida para produção.

### Assets e interações

Inventário atual de `public/assets/`: **11 arquivos, 1.966.419 bytes**. São nove recursos públicos — um CSS, três scripts, quatro imagens SVG/PNG e um PDF — e dois arquivos `.gitkeep` vazios, que não precisam ser exportados. O maior recurso é `images/meu-retrato.png`, com 1.864.678 bytes. A árvore de saída está na seção 4.

- A tipografia usa fontes do sistema (`system-ui` e `ui-monospace`). Não há arquivos de fonte, `@font-face` ou download de Google Fonts no portfólio. A menção a Google Fonts pertence à descrição de outro projeto.
- `theme-init.js` define o tema antes do CSS; `site.js` mantém botão, preferência em `localStorage`, cor do navegador, menu, foco, Escape e header ao rolar. Preservar ordem e atributos de carregamento.
- `hero-particles.js` usa Canvas e APIs do navegador, somente na home. Preservar reação ao tema, preferência de movimento reduzido, ponteiro, visibilidade e redimensionamento.
- Não foram encontradas chamadas `fetch()`/XHR nem dependência de endpoint PHP nos scripts. Os links de demonstração abrem aplicações externas e não integram o conteúdo a exportar.
- Preservar âncoras, nomes acessíveis, atributos `data-*`, IDs, `defer`, `download` e caminhos com maiúsculas, como `CurriculoLinkedin.pdf`.

### Dependências de geração

`composer.json` exige PHP `^8.2` e configura PSR-4 `App\\` → `app/`. O lock não contém pacotes externos. Composer continua útil para instalar/regenerar o autoload no ambiente de build, mas `vendor/` não será publicado.

O código utiliza Core/standard, PCRE, Filter, Hash e SPL; `getimagesize()` pertence a standard e não exige GD neste uso. É necessário acesso aos arquivos locais, suporte a SHA-256 e execução de PHP CLI. Não são necessários MySQL, PDO, SMTP ou bibliotecas dos projetos descritos no catálogo.

O isolamento proposto adicionará uso de `proc_open()` no gerador. Isso será requisito do ambiente de build, não da hospedagem. Para validar HTML com `DOMDocument`, prever `ext-dom` somente nas ferramentas de validação; ela está ausente no PHP local examinado e não é dependência atual da aplicação. Node será usado para os testes JavaScript e, se escolhido, para o CLI Wrangler; não para renderizar páginas no navegador.

## 3. Estratégia de geração

### Fluxo recomendado

```text
configurações + Models + Controllers + views + assets
                     |
              exportador PHP CLI
                     |
        renderização pelo public/index.php
                     |
        saída temporária completa + validação
                     |
                    dist/
                     |
        Cloudflare Pages OU GitHub Pages
```

Criar `scripts/export-static.php` como comando principal e `scripts/render-static-page.php` como adaptador CLI pequeno. Reutilizar a entrada real evita duplicar registro de handlers, regras de dados, tratamento de erros e inicialização.

1. Validar argumentos, destino, versão PHP, autoload, origem e prefixo. Exigir `APP_BASE_PATH` explicitamente, inclusive vazio, para não herdar `/portifolio` por acidente. O perfil de produção deve exigir origem HTTPS válida; desenvolvimento pode usar HTTP local.
2. Montar o manifesto de páginas: `/`, `/projetos` e cada `/projetos/{slug}` fornecido por **`Project::all()`**. Ler dados pelo Model, sem enumerar projetos diretamente de `config/projects.php` nem usar somente `featured()`.
3. Validar slugs e destinos únicos. Rejeitar segmentos inválidos, traversal, colisões, links simbólicos e escrita fora da pasta de saída prevista. Fixar `dist/` e sua área temporária no repositório simplifica o controle.
4. Criar uma árvore temporária vazia, ao lado de `dist/`. Gerar tudo do zero; o estado anterior não pode definir quais páginas continuam existindo.
5. Para cada página, iniciar um processo com `PHP_BINARY` e argumentos em array, sem montar comandos de shell com conteúdo. O adaptador configura `REQUEST_METHOD=GET`, `REQUEST_URI` com o prefixo correto, buffer de saída e status inicial 200, e inclui `public/index.php`.
6. Receber um envelope JSON com status e HTML; reservar stderr para diagnósticos. Exigir saída válida, código de processo zero, HTML não vazio e status **200** para todas as páginas públicas. A captura de 500 pelo front controller precisa reprovar o build, mesmo se o processo terminar com código zero.
7. Renderizar uma URI reservada e comprovadamente inexistente pela mesma entrada, exigindo status **404**, e salvar seu HTML como `404.html`. A URI é uma entrada de build, não uma nova rota pública. Um processo por renderização evita vazamento de status 404, headers ou variáveis entre páginas.
8. Copiar os assets aprovados de `public/assets/` mantendo nomes e bytes. Calcular os hashes com os arquivos definitivos, antes de qualquer comparação com os HTMLs. Não minificar ou transformar nesta primeira versão.
9. Gerar sitemap, robots e os arquivos auxiliares do destino. Validar a árvore inteira antes de substituir a saída anterior. Relatórios de build ficam fora de `dist/` e contêm apenas dados públicos/operacionais necessários.
10. Em caso de sucesso, substituir a saída completa; em caso de erro, retornar código não zero e manter a versão anterior. Limpeza fica restrita às pastas geradas conhecidas. Nunca publicar resultado parcial ou continuar usando `dist/` antiga após falha.

O exportador apenas mapeia URLs para arquivos. O Router continua responsável por despachar requisições; nenhuma regra de negócio vai para os scripts de build.

As rotas fixas atuais podem ser declaradas em uma lista pequena no exportador. Como o Router não expõe inventário, **novas rotas públicas fixas precisarão ser adicionadas ao manifesto e ao teste de cobertura de exportação**. Novos projetos visíveis serão incluídos automaticamente pelo Model. Não usar crawler recursivo de links como fonte de verdade, pois ele pode omitir páginas sem links e alcançar URLs externas.

### Contrato proposto do comando

Exemplos futuros, ainda não disponíveis nesta etapa:

```sh
# Cloudflare Pages ou site na raiz de domínio próprio
APP_BASE_PATH='' APP_ORIGIN='https://portfolio.example' php scripts/export-static.php --target=cloudflare

# Site de projeto no GitHub Pages; confirmar o nome exato do repositório
APP_BASE_PATH='/Portifolio' APP_ORIGIN='https://heitorsv001.github.io' php scripts/export-static.php --target=github

php scripts/validate-static.php
```

`portfolio.example` é ilustrativo e não é domínio aprovado. `/Portifolio` corresponde ao nome indicado no conteúdo atual, mas deve ser confirmado nas configurações reais do GitHub Pages. O adaptador interno receberá `APP_STATIC_EXPORT=1` somente durante a geração. O usuário não deve precisar editar a configuração local para alternar os destinos.

## 4. Estrutura proposta de `dist/`

```text
dist/
├── index.html
├── 404.html
├── projetos/
│   ├── index.html
│   ├── gerenciador-de-permanencias/index.html
│   ├── crowns/index.html
│   ├── festividades-de-fim-de-ano/index.html
│   └── noticias/index.html
├── assets/
│   ├── css/style.css
│   ├── js/
│   │   ├── theme-init.js
│   │   ├── site.js
│   │   └── hero-particles.js
│   ├── images/
│   │   ├── meu-retrato.png
│   │   ├── social-preview.png
│   │   ├── social-preview.svg
│   │   └── favicon.svg
│   └── documents/CurriculoLinkedin.pdf
├── robots.txt
├── sitemap.xml
└── .nojekyll                    # perfil GitHub
```

O inventário é dinâmico: futuras imagens e fontes públicas aprovadas acompanham `assets/` na mesma estrutura. Arquivos ocultos, fixtures de testes e links simbólicos não entram na cópia. Todos os assets atuais são recursos públicos; se no futuro um projeto oculto tiver materiais privados, esses materiais não devem ficar na árvore pública copiada. Ocultar uma rota não torna um asset copiado privado.

O prefixo `/Portifolio` existe nas URLs, **não** como uma pasta adicional `dist/Portifolio/`: o GitHub Pages monta o conteúdo do artefato no caminho do repositório.

Não incluir `app/`, `config/`, `vendor/`, `tests/`, `scripts/`, `design/`, `storage/`, `.git/`, `.env`, `.htaccess`, arquivos PHP, Composer, documentação ou relatórios na saída. Publicar a pasta `dist/` como raiz do artefato, nunca a raiz do repositório nem `public/` com o front controller.

`CNAME`, `_redirects` e `_headers` não são necessários para a primeira geração. Só adicioná-los ao perfil pertinente quando houver domínio ou regra concreta. Não incluir Functions ou `_worker.js`.

## 5. Rotas, URLs, downloads e metadados

### Páginas em diretórios e barra final

Adotar `projetos/index.html` e `projetos/{slug}/index.html` nos dois provedores. Os slugs permanecem idênticos. A mudança visível é a preferência por `/projetos/` e `/projetos/{slug}/`; acessos antigos sem a barra devem alcançar a página, normalmente após redirecionamento do provedor. Validar isso no destino escolhido.

Não gerar simultaneamente `projetos.html` e `projetos/index.html`. Isso cria formas concorrentes da mesma página. Não depender de `.htaccess`, rewrites para PHP, redirecionamento JavaScript nem fallback universal para a home.

### Adaptação mínima no código, em implementação futura

1. Acrescentar a `config/app.php` uma opção de exportação, derivada de `APP_STATIC_EXPORT`, desativada por padrão.
2. Concentrar em `app/Core/Controller.php` uma função de URL de página, disponibilizada às views como `$pageUrl`. Ela combina `$basePath` e o caminho lógico, acrescentando barra final somente para páginas no modo estático. Preservar o comportamento MVC padrão.
3. Usar essa função para `$homeUrl`, `$projectsUrl` e o caminho do canonical/`og:url`. A origem continua sendo validada pela lógica existente. Não acrescentar barra a imagens, scripts, CSS, PDF ou outras URLs de arquivo.
4. Trocar apenas a montagem de href de detalhe em `partials/project-list.php` por `$pageUrl('/projetos/' . rawurlencode($project['slug']))`. Alterar `$projectsUrl` sem esse ajuste produziria `//` na concatenação atual.
5. Os demais templates continuam usando as variáveis atuais. Preservar escaping nos atributos e textos e não fazer substituição global com regex no HTML renderizado.

### Prefixo e origem por ambiente

| Destino | `APP_ORIGIN` | `APP_BASE_PATH` | Exemplo de detalhe |
| --- | --- | --- | --- |
| Cloudflare Pages | `https://<projeto>.pages.dev` | vazio | `https://<projeto>.pages.dev/projetos/crowns/` |
| GitHub Pages de projeto | `https://heitorsv001.github.io` | `/Portifolio`, se confirmado | `https://heitorsv001.github.io/Portifolio/projetos/crowns/` |
| GitHub Pages de usuário | `https://heitorsv001.github.io` | vazio | `https://heitorsv001.github.io/projetos/crowns/` |
| Domínio próprio na raiz | origem HTTPS confirmada | vazio | `<origem>/projetos/crowns/` |

Usar URLs iniciadas por `/`, já com o prefixo, para navegação e assets. Elas funcionam em qualquer profundidade, inclusive quando a 404 é exibida para `/qualquer/caminho/inexistente`. Evitar `../../assets`, `<base href>` e caminhos físicos como `/var/www/...`, `/public` ou `/htdocs` no HTML.

Manter as âncoras da home como `<prefixo>/#sobre`, `/#contato` etc. Skip link `#conteudo` e retorno `#topo` continuam locais. Se surgirem URLs relativas em CSS, resolvê-las em relação ao próprio CSS, sem adicionar prefixo duas vezes; atualmente não há `url()` no stylesheet.

Copiar o PDF e preservar o atributo `download` em um endereço do mesmo site. Conferir conteúdo e MIME no host; um redirecionamento para outro domínio pode alterar o comportamento de download. Não transformar o PDF em rota HTML. Redes, demonstrações, repositórios e `mailto:` mantêm seus endereços atuais, sem prefixo ou barra extra.

### SEO

- Renderizar os metadados existentes, mantendo títulos e descrições de cada Controller e Open Graph completo no HTML inicial.
- No modo estático, canonical, `og:url`, navegação e sitemap devem concordar sobre a barra final e o prefixo. `og:image` continua sendo URL absoluta de arquivo, com dimensões, tipo e texto alternativo atuais.
- O build de produção deve falhar se a origem estiver ausente/inválida ou se uma página válida não tiver seus metadados esperados. O comportamento local atual de omitir URLs absolutas pode permanecer fora da exportação de produção.
- Gerar `sitemap.xml` com as seis páginas atuais e somente projetos visíveis. Não listar 404, rotas inexistentes, projeto oculto ou `index.html` como URL canônica. Não inventar `lastmod` a cada build; omitir se não houver data confiável do conteúdo.
- Gerar `robots.txt` permissivo para produção e referenciar o sitemap absoluto. Em GitHub Pages de projeto, `/Portifolio/robots.txt` não controla os robôs da origem: o arquivo efetivo é `/robots.txt`. Publicar o sitemap no prefixo e submetê-lo separadamente, sem prometer controle do arquivo da raiz.
- Para prévias, usar artefato/perfil de preview com `noindex` nas páginas e sem confundir a origem de produção com a URL temporária. Se o preview usar o canonical de produção, ele deve continuar apontando para a publicação principal. Não usar somente `Disallow` para tentar garantir ausência de indexação.
- Se os dois provedores forem usados ao mesmo tempo, escolher um endereço principal para canonical e sitemap; gerar cada artefato com o prefixo correto. Preferir um destino de produção inicialmente.

### Página 404

Reutilizar `ErrorController` e `app/Views/errors/404.php`, incluindo layout, tema, header e botão para a home. Salvar na raiz do artefato como `404.html`. O arquivo não deve anunciar canonical/OG de página válida e deve manter `noindex, follow`.

A captura de status PHP valida o conteúdo durante a geração, mas não configura o servidor estático. O host deve responder **404 na URL inexistente**, exibindo esse HTML, sem redirecionar para `/404.html` com 200. Abrir `/404.html` diretamente não comprova esse comportamento.

Na Cloudflare, o `404.html` da raiz também impede que o Pages assuma o fallback de SPA. A plataforma documenta busca por 404 na árvore e normalização de páginas HTML; isso se aplica ao perfil proposto. [Entrega de páginas no Cloudflare Pages](https://developers.cloudflare.com/pages/configuration/serving-pages/).

O GitHub Pages documenta o uso de `404.html` na origem de publicação. Testar também um caminho inexistente profundo sob o prefixo do repositório. [404 personalizada no GitHub Pages](https://docs.github.com/en/pages/getting-started-with-github-pages/creating-a-custom-404-page-for-your-github-pages-site).

## 6. Arquivos a criar ou modificar na implementação

| Arquivo | Trabalho proposto |
| --- | --- |
| `scripts/export-static.php` — novo | Manifesto, configuração do build, subprocessos, cópia de assets, sitemap/robots, validação e substituição completa da saída. |
| `scripts/render-static-page.php` — novo | Adaptador restrito a CLI, captura de HTML/status pela entrada existente e resposta estruturada. |
| `scripts/validate-static.php` — novo | Verificar HTML/URLs/arquivos/SEO e inventário permitido do artefato; usar parser HTML, com `ext-dom` no ambiente de ferramentas. |
| `tests/static-export.php` — novo | Integração do exportador, destinos raiz/prefixo, exclusão de ocultos e reconstrução sem arquivos antigos. |
| `config/app.php` | Opção de modo estático desativada no desenvolvimento MVC habitual. |
| `app/Core/Controller.php` | Função de URL de página e uso consistente na navegação e metadados. |
| `app/Views/partials/project-list.php` | Ajuste pontual de href de detalhe para a função compartilhada. |
| `tests/run.php` | Cobertura das URLs no modo estático, mantendo expectativas atuais do modo MVC; tratar separadamente a divergência de contato já detectada. |
| `.gitignore` | Ignorar `dist/`, área temporária e relatórios gerados. |
| `README.md` | Requisitos e comandos de geração, preview, validação, escolha de prefixo/origem e rotina de atualização. |
| `.github/workflows/static-pages.yml` — futuro | Validação/build e publicação no provedor escolhido, habilitada somente na etapa de automação autorizada. |

Não é necessário modificar Router, Models, Controllers específicos, `public/index.php`, layout, estilos ou JavaScript para viabilizar a estratégia. Os tipos documentados das configurações no Core podem ser ampliados para a opção nova. Metadados de preview podem usar a flag já passada ao layout, preservando os valores de produção.

Sitemap, robots, `.nojekyll` e eventuais arquivos de host serão saídas geradas, sem uma segunda cópia manual do conteúdo. Novos requisitos reais podem alterar essa lista, mas não justificam uma refatoração geral nesta etapa.

## 7. Compatibilidade com os provedores

### Cloudflare Pages

Compatível com HTML, CSS, JS e PDF gerados. Usar preset de site estático, com conteúdo publicado correspondente a `dist/`. A documentação permite publicar HTML sem framework. [HTML estático no Pages](https://developers.cloudflare.com/pages/framework-guides/deploy-anything/).

**Caminho recomendado: build local ou no GitHub Actions e envio do artefato pronto por Direct Upload.** Isso permite controlar PHP 8.2+ sem depender da imagem de build do Pages. Na documentação consultada, as tabelas modernas não oferecem garantia explícita de PHP 8.2+ e a tabela antiga lista versões incompatíveis. Não presumir que definir `PHP_VERSION=8.2` resolverá. A integração Git nativa só deve ser escolhida após comprovar PHP/Composer compatíveis ou preparar um ambiente de build apropriado. [Imagem de build do Pages](https://developers.cloudflare.com/pages/configuration/build-image/).

A saída pode ser enviada manualmente pelo painel ou por Wrangler; a ferramenta de envio não altera a aplicação. O envio externo via CI mantém a reconstrução automática sem exigir PHP na Cloudflare. Um projeto criado como Direct Upload não pode ser convertido para integração Git nativa; essa escolha precisa ser feita na configuração inicial. [Direct Upload](https://developers.cloudflare.com/pages/get-started/direct-upload/).

O volume atual é pequeno frente aos limites publicados do plano gratuito: 20.000 arquivos por site e 25 MiB por arquivo. Não há necessidade de Functions ou armazenamento separado para os assets atuais. Conferir limites novamente na implementação. [Limites do Cloudflare Pages](https://developers.cloudflare.com/pages/platform/limits/).

### GitHub Pages

Compatível com o artefato estático; PHP será executado apenas no build. Usar **GitHub Actions como fonte de publicação**, com upload de `dist/`, sem manter uma branch de HTMLs gerados nem passar por Jekyll.

O site de projeto recebe o nome do repositório no caminho; o site de usuário exige o repositório `<owner>.github.io` e fica na raiz. Confirmar o caminho com os metadados/configurações do Pages, respeitando maiúsculas. Em conta GitHub Free, a documentação disponibiliza Pages em repositórios públicos; não foi consultada a configuração da conta ou do repositório remoto. [Tipos e disponibilidade do GitHub Pages](https://docs.github.com/en/pages/getting-started-with-github-pages/what-is-github-pages).

Incluir `.nojekyll` como indicação de conteúdo pronto, sem tornar o build dependente de Jekyll. `.htaccess`, `_redirects` e `_headers` não fazem parte da configuração deste destino. A estratégia de diretórios dispensa essas regras e não depende de reescrita arbitrária no host.

O site está muito abaixo do limite publicado de 1 GB. Disponibilidade gratuita e cotas devem ser reconferidas ao ativar o destino. [Limites do GitHub Pages](https://docs.github.com/en/pages/getting-started-with-github-pages/github-pages-limits).

**Escolha sugerida:** GitHub Pages com Actions se o repositório público e o prefixo forem aceitáveis; Cloudflare Pages com build externo se for desejável um subdomínio na raiz e maior controle futuro da entrega. Os dois preservam a mesma fonte MVC, com artefatos separados por configuração.

## 8. Publicação, atualização e GitHub Actions

### Primeira publicação

1. Implementar e validar localmente o exportador, sem acessar a hospedagem.
2. Definir provedor, endereço principal, prefixo e branch de produção. Conferir se existe publicação anterior que exija migração de URLs.
3. Gerar o artefato para esse endereço, conferir rotas, assets, SEO e 404 em servidor estático local. Não editar `dist/` manualmente.
4. Após autorização de publicação, configurar o projeto/Pages e enviar somente o artefato aprovado.
5. Verificar URLs públicas, status, MIME, downloads e recursos no navegador. Essas verificações reais são necessárias para aprovar a entrega do host.

### Reconstrução após alteração de conteúdo

Editar `config/profile.php`, `config/projects.php`, `config/skills.php`, `config/contact.php`, views ou assets; executar testes, reconstruir integralmente, validar e substituir a publicação pelo novo artefato. Alterar origem/prefixo também exige reconstrução completa, pois os endereços ficam gravados no HTML.

Projetos adicionados com visibilidade pública entram automaticamente. Projetos removidos ou ocultados deixam de existir na próxima saída. Um teste deve gerar com um projeto visível, ocultá-lo em fixture, reconstruir e comprovar ausência do arquivo antigo. A publicação deve representar a árvore completa, sem upload incremental que retenha HTML obsoleto.

Manter artefato aprovado anterior e identificação da revisão/configuração usada para permitir rollback. Restaurar um artefato antigo pode reexpor conteúdo que foi ocultado depois; conferir esse ponto antes de reverter. Um build com erro não deve disparar publicação nem reutilizar silenciosamente uma saída anterior.

### Automação futura

Fluxo recomendado, sem YAML criado nesta etapa:

1. `pull_request`: checkout, PHP em versão 8.2+ explicitamente definida, Composer, Node para testes JS e `ext-dom` para o validador; testar, gerar e validar. Disponibilizar artefato para revisão, sem publicar produção nem expor credenciais a código de forks.
2. `workflow_dispatch`: execução manual inicial. Depois de validado e autorizado, `push` na branch de produção pode reconstruir e publicar automaticamente. Não haverá necessidade de editar HTML ou pedir confirmação a cada atualização já coberta pela política aprovada.
3. Executar `composer install --no-interaction --prefer-dist --no-dev --optimize-autoloader` com o lock, testes PHP/JS, exportação e validação. Falhas interrompem o fluxo; a divergência atual da suíte precisa ser resolvida antes de exigir CI verde.
4. Gerar para a origem e base reais do destino, não a URL do runner. Separar jobs de build e deploy com dependência explícita e controle de concorrência para impedir que uma revisão antiga substitua a nova.
5. No GitHub Pages, usar `actions/configure-pages`, `actions/upload-pages-artifact` com `path: dist` e `actions/deploy-pages`. Restringir `pages: write` e `id-token: write` ao job de deploy, com ambiente `github-pages`; build usa leitura do repositório. O workflow e o ambiente definem quais branches podem publicar. [Workflows personalizados para Pages](https://docs.github.com/en/pages/getting-started-with-github-pages/using-custom-workflows-with-github-pages).
6. Na Cloudflare, usar Wrangler para `pages deploy dist` com o projeto/branch corretos, token limitado a Pages da conta e credenciais em secrets do Actions. Fixar versões das ferramentas/actions na implementação. Node/CLI ficam no CI; não são uma dependência nova do front-end. [Direct Upload com integração contínua](https://developers.cloudflare.com/pages/how-to/use-direct-upload-with-continuous-integration/).
7. Observar mudanças em `app/**`, `config/**`, `public/assets/**`, scripts, testes, manifestos Composer e no workflow. Mudanças apenas documentais podem dispensar reconstrução; sempre permitir disparo manual. Escolher um único mecanismo de deploy por destino para evitar duplicidade.

## 9. Testes e critérios de validação

### Verificações realizadas nesta análise

| Verificação | Resultado observado |
| --- | --- |
| PHP CLI | 8.4.27. |
| Sintaxe PHP | **30 arquivos sem erros**, incluindo `app/`, `public/`, `config/`, `tests/` e `index.php` da raiz. |
| Suíte `php -d error_reporting=E_ALL tests/run.php` | Executada em cópia temporária: **1.600/1.601 verificações aprovadas**, processo com código 1. |
| Falha da suíte | `Currículo isolado não gera lista vazia de contatos`. O teste em `tests/run.php` exige ausência de `class="contact-links"` quando só existe currículo, mas a view `partials/contact.php` inclui o PDF nessa lista. É uma divergência já presente entre teste e markup, sem alteração nesta tarefa. |
| Renderização real do front controller em CLI | **16/16 casos**: seis páginas válidas, projeto oculto e rota inexistente, tanto na raiz quanto em `/Portifolio`, usando origem ilustrativa. Status, canonical/noindex e metadados esperados conferidos; referências ao PDF conferidas nas duas bases. |
| Composer e Node | Não encontrados no PATH. Não executados Composer, testes JS ou instalação de dependências. Autoload já existente utilizado nas renderizações. |
| Parser DOM | `DOMDocument` ausente; planejar `ext-dom` para o futuro validador. |
| HTTP, navegador, artefato estático e host | Não testados nesta etapa. Não existe ainda `dist/` gerada por este plano. |

Os testes PHP criam imagens e documentos temporários. Por isso, a suíte foi executada em uma cópia temporária, sem inserir fixtures na árvore original. Resultados históricos de 1.601/1.601 e de validação no Chrome constam nos documentos do redesign, mas não substituem o resultado atual acima. Investigar a expectativa do cenário de currículo em alteração separada, preservando o download aprovado.

### Regressões existentes a executar na implementação

```sh
composer validate --no-check-publish
php -d error_reporting=E_ALL tests/run.php
find app public config tests scripts -name '*.php' -print0 | xargs -0 -n1 php -l
php -l index.php
node tests/theme.js
node tests/navigation.js
node tests/particles.js
php tests/static-export.php
```

Executar na árvore candidata isolada quando houver fixtures. Os comandos com `scripts/` e `tests/static-export.php` só serão válidos depois da implementação. As suítes JavaScript existentes usam objetos simulados e não substituem inspeção no navegador.

### Validação automatizada do artefato

- Confirmar exatamente seis páginas públicas atuais e `404.html`, relacionando o manifesto ao inventário final. Validar títulos, descrições, idioma, um `h1`, canonical, `og:url` e imagem absoluta válida quando aplicável.
- Resolver `href`, `src`, futuros `srcset` e referências CSS contra a URL de cada documento, respeitando prefixo, query e fragmento. Conferir arquivos existentes, IDs de âncora no destino e ausência de prefixos duplicados. Ignorar query `?v=` ao procurar o arquivo e verificar seu hash separadamente.
- Conferir imagens, favicon, scripts, CSS e PDF por existência e bytes; comparar os assets copiados com a fonte. Não seguir `mailto:` ou links externos como caminhos locais.
- Confirmar ausência de `portfolio-mvc-php` nos arquivos de rota, cards, links e sitemap. Não proibir cegamente palavras isoladas que possam existir legitimamente em outros contextos.
- Confirmar ausência de PHP executável, código-fonte privado, fixtures, relatórios e caminhos físicos. Reprovar warnings/stack traces no HTML e falhas de renderização.
- Testar origem ausente/inválida, slug inválido/duplicado, falha de escrita e 500 de renderização: todos devem interromper o build, preservando a saída anterior.
- Repetir geração sem mudanças e comparar bytes/inventário. Não incluir timestamps variáveis no HTML ou `lastmod` inventado. Testar reconstrução após ocultar/remover projeto, sem resíduos.
- Preservar as saídas MVC normais sem barra final e conferir as saídas estáticas com barra final, na raiz, em `/Portifolio` e em um prefixo com mais de um segmento.

### HTTP e navegador

Servir **somente o artefato** em um servidor estático local, em vez de abrir via `file://` ou permitir fallback para PHP. Para testar o perfil de projeto, montar o conteúdo de `dist/` sob o prefixo correspondente em uma raiz temporária; servir `dist/` diretamente na raiz não reproduz `/Portifolio`.

| Área | Critério de aceite |
| --- | --- |
| Páginas | Home, catálogo e quatro detalhes com GET 200; acesso direto e recarregamento funcionam. |
| Formas da URL | Sem barra, com barra, query e `index.html` alcançam a página; links gerados e canonical usam a forma escolhida. Conferir os redirecionamentos do host. |
| Erros | Rota inexistente, slug oculto e URL profunda inexistente retornam 404 com a página correta. Nunca home com 200. |
| Assets | CSS/JS/imagens/PDF com MIME e bytes corretos, sem HTML de fallback ou 404. |
| Downloads | PDF abre/baixa pelo link real; nome e atributo `download` preservados. |
| Temas | Claro/escuro, persistência, preferência do sistema e armazenamento bloqueado; início do carregamento sem regressão de tema. |
| Menu | Mobile/desktop, redimensionamento, Escape, clique externo, fechamento por link, Tab e restauração do foco. |
| Partículas | Canvas na home, aparência em ambos os temas, mouse, pausa fora da viewport/aba oculta e movimento reduzido; ausência nas outras páginas. |
| Navegação | Todas as âncoras, skip link, retorno ao topo, home a partir de detalhes/404, catálogo e links externos. |
| Layout | Comparar PHP e estático em 320, 390, 768 e 1440px, nos dois temas; ausência de overflow e IDs duplicados. |
| Sem JS | Conteúdo e links essenciais disponíveis; comportamento progressivo original preservado. |
| SEO | Metadados presentes na resposta HTML, URLs públicas sem caminhos locais e imagem OG acessível. |
| Console/rede | Sem novos erros de JavaScript, downloads de assets ausentes ou requisições a PHP. |

A configuração de 404 de um servidor local pode diferir da hospedagem. Usar preview que suporte fallback 404 com esse status ou configurá-lo apenas no ambiente de teste; um servidor simples pode exibir seu próprio erro. Repetir os testes HTTP essenciais no provedor após publicação autorizada, incluindo HEAD e POST para documentar as diferenças de método.

## 10. Etapas de implementação

| Etapa | Entrega | Critério para avançar |
| --- | --- | --- |
| 1. Base de validação | Investigar a divergência do teste de currículo; preparar PHP/Composer/Node e parser de HTML no ambiente de ferramentas. | Resultado dos testes compreendido e expectativa de contato alinhada, sem perda do download. |
| 2. URLs de exportação | Flag de build e função central de URL; ajuste pontual no link dos cards. | MVC padrão preservado; modo estático com prefixo/barra/SEO consistentes. |
| 3. Exportador | Manifesto via Model, renderização isolada, assets, 404 e saída temporária. | Seis páginas + erro; projeto oculto ausente; erros interrompem build; reconstrução elimina páginas antigas. |
| 4. Metadados e verificador | Origem obrigatória em produção, sitemap/robots e validação de links/arquivos. | Artefatos válidos para raiz e prefixo; nenhum caminho local ou arquivo privado publicado. |
| 5. Regressão e documentação | Testes PHP/JS/exportador, comparação visual e instruções no README. | Critérios de HTTP, acessibilidade e interações atendidos no preview estático. |
| 6. Primeira publicação | Configuração do destino e envio do artefato, após autorização. | Status reais, rotas, PDF, assets e SEO conferidos no endereço público. |
| 7. Automação | Actions para reconstrução e publicação segundo branch/ambiente aprovados. | Alteração de conteúdo gera nova versão; falha não publica; rollback documentado. |

Pendências para a execução: escolher provedor/endereço final e confirmar o prefixo; alinhar o teste de contato; disponibilizar ferramentas ausentes; implementar e validar o exportador. Nenhuma dessas pendências impede a viabilidade arquitetural. Este plano encerra a etapa documental e não representa autorização para implementação, configuração de conta, deploy, commit ou push.
