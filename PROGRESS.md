# Progresso e passagem de contexto

Atualizado em **18/10/2026**. A seção 0 registra o estado atual após a etapa 6. As demais seções preservam o histórico das etapas 1 a 5 e devem ser interpretadas como estados anteriores. Conferir novamente o ambiente na próxima sessão.

## 0. Entrega atual — etapa 6: habilidades e tecnologias

Implementada exclusivamente a próxima etapa pendente, a etapa 6, conforme o pedido do proprietário. Etapas 1 a 6 concluídas funcionalmente; **etapa 7 não iniciada**. Árvore de trabalho limpa no início, no commit `91e47ed` (`Implementa catálogo e páginas individuais dos projetos`). A pedido do proprietário, esta entrega é reunida no commit `Implementa habilidades e tecnologias da etapa 6`; consultar `git log` para o hash. Sem push.

### Alterações realizadas

| Arquivo ou área | Alteração desta etapa |
|---|---|
| `config/skills.php` (novo) | Fonte de habilidades confirmadas por categoria; retorna lista vazia até o proprietário fornecer os dados. |
| `app/Models/Skill.php` (novo) | Carrega grupos e oferece `all()`; remove espaços nas extremidades, itens em branco e categorias sem nome ou itens. |
| `app/Controllers/HomeController.php` | Recebe o Model por injeção e envia `skillGroups` à home. Dependências anteriores preservadas. |
| `public/index.php` | Injeta `Skill` no HomeController usando o autoload Composer existente. |
| `app/Views/partials/skills.php` (novo) e `home/index.php` | Seção reutilizável após os projetos, com categorias, listas e aviso explícito enquanto não houver habilidades confirmadas. |
| `app/Views/partials/header.php` | Link para a seção da home em todas as páginas, respeitando o caminho-base. |
| `public/assets/css/style.css` | Grid fluido e apresentação das categorias/listas usando os tokens existentes. |
| `tests/run.php` | Fixtures isoladas para dados de habilidades, atualização por arquivo, estado vazio, escaping, semântica e navegação. |
| `README.md` e `PROJECT_CONTEXT.md` | Formato dos dados, manutenção da seção, verificações e checklist da etapa 6. |

### Decisões técnicas e limites de escopo

- Separação MVC preservada: configuração é lida apenas pelo Model; Controller consulta e passa dados; partial apresenta texto escapado. Não foram alterados Router, Core, dados dos projetos ou páginas individuais.
- Esquema simples: lista de grupos com `category` (string) e `items` (lista de strings), na ordem do arquivo. Nomes de categorias são configuráveis, sem lista fixa no layout. Categorias sem conteúdo são omitidas; ausência de grupos utilizáveis produz o mesmo aviso de habilidades pendentes.
- `Skill` aceita arquivo alternativo para testes, seguindo o padrão de `Project`. O parâmetro adicional do HomeController mantém as chamadas existentes compatíveis; não há infraestrutura genérica de validação ou persistência adicional.
- `config/skills.php` real permanece vazio. Referências de habilidades no contexto e tecnologias dos projetos não confirmam conhecimentos pessoais. Nenhuma habilidade, nível de proficiência, experiência ou contato foi inventado. Exemplos preenchidos existem somente em fixtures temporárias e não foram publicados.
- Seção `#habilidades` usa h2, categorias em h3, listas nativas e destino focável por fragmento com `tabindex="-1"`. Não foram gerados IDs a partir de categorias; texto arbitrário permanece texto. Navegação de catálogo, detalhe e 404 retorna à home com o prefixo correto.
- CSS usa o tema e os espaços existentes, com colunas fluidas e quebra de textos longos. Sem JavaScript, ícones ou requests externos, novas dependências, banco de dados, alterações de Apache ou permissões. Contatos e demais etapas permanecem fora desta entrega.

### Testes e evidências desta entrega

| Verificação | Resultado |
|---|---|
| Suíte antes das mudanças | **589/589** aprovadas. |
| `php -d error_reporting=E_ALL tests/run.php` | **696/696** aprovadas. |
| Sintaxe em `app`, `public`, `config`, `tests` e `index.php` | **24 arquivos PHP**, sem erros. |
| `git diff --check` | Aprovado. |
| Fixtures isoladas | Categorias/itens na ordem correta, trimming, omissão de vazios, inclusão por arquivo, escaping de nomes e habilidades, aviso pendente e ausência de inferência a partir dos projetos aprovados. |
| Raiz, `/portifolio` e subdiretório aninhado | Layout, IDs, headings associados e links de ida/retorno à seção conferidos nos testes. |
| Apache real | GET home/catálogo **200**; rota inexistente e detalhe desconhecido **404**; nova seção e aviso presentes no HTML executado. |
| Apache: novos arquivos privados | `config/skills.php` e `app/Views/partials/skills.php` **403**. |
| CSS via HTTP | **200** e idêntico ao arquivo atualizado no Apache e no servidor PHP temporário. |
| Cópia temporária + servidor PHP com raiz `public/` | Home preenchida, catálogo e detalhe de fixture **200**; rota desconhecida **404**. Categorias, escaping, atualização apenas dos dados e retorno ao estado vazio aprovados com autoload Composer real. |
| HTML servido | H1 e IDs únicos, headings referenciados existentes e âncoras locais/da home válidas; nenhuma exposição de código PHP. |

O acesso HTTP exigiu escalada do sandbox após a tentativa local sem escalada não conectar. Foi usado GET para a validação funcional: o Router existente registra apenas GET e continua retornando 404 para HEAD. Nenhuma configuração de servidor foi alterada. A cópia temporária continha fixtures de habilidades e um projeto apenas para verificar integração e navegação; foi removida e seu servidor encerrado, sem avisos ou erros de aplicação no log. Os arquivos de dados reais permaneceram vazios. Nenhuma alteração nas dependências ou no autoload foi necessária.

### Aceite, pendências e próxima retomada

- **Aceite funcional da etapa 6 atendido:** seção integrada ao layout, conhecimentos organizáveis por categorias, partial reutilizável e atualização apenas pelo arquivo de dados. O estado preenchido foi validado com fixtures; a versão real sinaliza a falta de confirmação das habilidades.
- **Conteúdo pendente:** proprietário deve confirmar as habilidades e suas categorias antes de preencher `config/skills.php`. Nome, biografia, projetos e contatos continuam pendentes conforme o contexto.
- **Validação manual pendente:** legibilidade em mobile/desktop, textos longos e zoom, console, foco e Tab/Enter no menu “Habilidades”, inclusive a partir de catálogo, detalhe e 404. Inventário desta sessão: `apps: []`, `browsers: []`; não foi possível executar inspeção visual ou teclado real. Testes HTML/HTTP não substituem essa revisão.
- Próxima etapa de implementação: **etapa 7 — contato e redes profissionais**, somente após novo pedido. Utilizar apenas links e dados confirmados; não adicionar formulário de contato.

## Histórico — etapa 5: páginas individuais dos projetos

Implementada exclusivamente a etapa 5, a pedido do proprietário. Etapas 1 a 5 concluídas funcionalmente; **etapa 6 não iniciada**. A árvore de trabalho já continha a entrega da etapa 4, sem commit, sobre `47c0546`; essas alterações foram preservadas. A pedido do proprietário, as entregas das etapas 4 e 5 são reunidas no commit `Implementa catálogo e páginas individuais dos projetos`; consultar `git log` para o hash. Sem push.

### Alterações realizadas

| Arquivo ou área | Alteração desta etapa |
|---|---|
| `app/Core/Router.php` | Reconhece parâmetros de um segmento em rotas GET; prioriza rotas fixas e passa parâmetros decodificados ao handler. |
| `app/Models/Project.php` | Adiciona `findBySlug()`, validando formato e procurando correspondência exata nos dados já normalizados. |
| `app/Controllers/ProjectController.php` | Adiciona `show()`: consulta o Model, renderiza detalhe ou delega ao Controller 404 existente. |
| `public/index.php` | Registra GET `/projetos/{slug}` e reutiliza a instância do ProjectController. |
| `app/Views/projects/show.php` (novo) | Página individual com título, resumo, descrição completa, imagem e opcionais; retorno ao catálogo. |
| `app/Views/partials/project-list.php` | Títulos dos cards viram links para os detalhes na home e no catálogo. |
| `app/Views/partials/project-technologies.php` e `project-links.php` (novos) | Extraem markup dos cards para reutilização na página individual, preservando escaping e nomes acessíveis. |
| `public/assets/css/style.css` | Layout do detalhe, apresentação da imagem e quebras de linha na descrição. |
| `tests/run.php` | Amplia testes para Router parametrizado, busca por slug, detalhes, navegação, opcionais, escaping e 404. |
| `README.md` e `PROJECT_CONTEXT.md` | Documenta rotas, formato dos slugs, descrições, evidências e checklist da etapa 5. |

### Decisões técnicas e limites de escopo

- Router permanece genérico: parâmetros como `{slug}` ocupam um segmento, são decodificados por `rawurldecode` uma única vez e enviados por posição ao handler. Rotas fixas têm prioridade independentemente da ordem de registro. Caminho-base, query string, barra final, métodos e retorno booleano de despacho existentes foram preservados.
- A validação específica fica no Model: formato `^[a-z0-9]+(?:-[a-z0-9]+)*$`, com correspondência exata e sensível a maiúsculas. Slug vazio, barras decodificadas, travessia, espaços, acentos, hífens duplicados/extremos e controles não são aceitos. Desconhecidos e inválidos retornam `null`; o Controller responde com a página/status 404 existentes, sem expor entradas ou caminhos internos.
- Detalhes utilizam o layout compartilhado, título próprio, h1 único e seções semânticas. O menu não indica a home ou o catálogo como página atual durante a visualização do detalhe. Link explícito retorna ao catálogo, respeitando o caminho-base.
- Descrição é texto simples com escaping; CSS `white-space: pre-line` preserva quebras de linha. Nenhum HTML arbitrário, Markdown, editor ou sanitizador adicional foi introduzido. Títulos, resumos, tecnologias, atributos e links também são escapados.
- Imagem e URLs reutilizam a normalização da etapa 4. No detalhe, imagem ausente é omitida e seções de tecnologias/links só aparecem quando há conteúdo. Imagem disponível é exibida sem corte intencional, com dimensões e texto alternativo. Cards mantêm o fallback e os links externos existentes.
- Partials de tecnologias e links são compartilhadas entre cards e detalhe, evitando duplicação de markup. URLs dos cards usam o slug codificado com `rawurlencode` e atributo escapado. Não houve mudança no arquivo de dados real, que permanece vazio, nem acréscimo de campos ao esquema.
- Fixtures fictícias foram usadas apenas em arquivos de teste e cópia temporária. Nenhum projeto, habilidade, contato ou informação biográfica foi atribuído ao proprietário. Sem novas dependências, JavaScript, banco de dados ou alterações no Apache/permissões. Habilidades e demais etapas não foram iniciadas.

### Testes e evidências desta entrega

| Verificação | Resultado |
|---|---|
| Suíte antes das mudanças | **320/320** aprovadas. |
| `php -d error_reporting=E_ALL tests/run.php` | **589/589** aprovadas. |
| Sintaxe em `app`, `public`, `config`, `tests` e `index.php` | **21 arquivos PHP**, sem erros. |
| `git diff --check` | Aprovado. |
| Router parametrizado | Captura/decodificação única, precedência de rotas fixas, query/barra final, prefixos, métodos e rejeição de segmentos extras aprovados. |
| Model/Controllers/Views isolados | Projeto correto por slug, item não destacado, descrição escapada com quebras, imagem/links/tecnologias, ausência de opcionais, título específico, layout único, IDs/headings, links dos cards e retorno ao catálogo aprovados. |
| Raiz, `/portifolio` e subdiretório aninhado | Detalhes e 404 testados via Router com fixtures; URLs e imagens respeitam caminho-base. |
| Slugs inválidos/desconhecidos | **404**, incluindo maiúsculas, controles, barras codificadas e codificação dupla; sem caminhos internos. |
| Apache real, catálogo vazio | Home/catálogo **200**; detalhe desconhecido, slug inválido e segmento extra **404**; POST ao detalhe **404**. |
| Apache: arquivos privados | `config/projects.php` e `app/Views/projects/show.php` **403**. |
| Cópia temporária + servidor PHP com raiz `public/` | Detalhes completos, mínimos e com opcionais inválidos **200**; fluxo card → detalhe → catálogo aprovado; imagem **200**, query/barra final e slug válido codificado **200**; inválidos/desconhecidos e POST **404**. |
| HTML/CSS via HTTP | Layout e h1 únicos, IDs e `aria-labelledby` válidos, âncoras existentes, texto escapado; CSS **200** e idêntico ao arquivo local nos dois servidores. |

A cópia de teste continha os mesmos arquivos `app/`, `public/`, autoload Composer e configuração de caminho-base, com dados fictícios apenas nessa cópia. Isso validou o front controller e o autoload sem cadastrar exemplos no catálogo real. Cópia e imagem temporárias foram removidas e o servidor foi encerrado, sem erros de aplicação no log. O Apache real não possui projetos cadastrados para medir um detalhe 200; esse caso foi validado na cópia e nos testes isolados. Acesso HTTP local e servidor temporário exigiram escalada do sandbox, sem mudança de configuração ou permissões.

### Aceite, pendências e próxima retomada

- **Aceite funcional da etapa 5 atendido:** cada item válido recebe URL própria por slug; detalhe mostra os dados do projeto correto e retorna ao catálogo; slug desconhecido/inválido responde 404. Inclusão continua sendo feita apenas pelo arquivo de dados.
- **Conteúdo:** projetos reais, títulos, descrições, tecnologias, imagens e links continuam pendentes de confirmação do proprietário. O catálogo real permanece vazio; nomes e biografia pendentes das etapas anteriores também foram preservados.
- **Validação manual:** verificar detalhe mobile/desktop e zoom, imagem, descrições longas, console e Tab/Enter nos títulos dos cards, retorno ao catálogo e links externos. O inventário retornou `apps: []` e `browsers: []`; inspeção visual e teclado real não puderam ser realizados. Testes de HTML/HTTP não substituem essa revisão.
- Próxima implementação: **etapa 6 — habilidades e tecnologias**, somente após novo pedido. Exibir apenas conhecimentos confirmados; não inferir habilidades pessoais pelas tecnologias usadas nesta aplicação ou nas fixtures.

## Histórico — etapa 4: catálogo de projetos

Implementada exclusivamente a etapa 4, a pedido do proprietário. Etapas 1 a 4 concluídas funcionalmente; **etapa 5 não iniciada**. Árvore de trabalho limpa no início, no commit `47c0546` (`Implementa página inicial e apresentação da etapa 3`). Alterações desta entrega mantidas na árvore de trabalho, sem commit ou push.

### Alterações realizadas

| Arquivo ou área | Alteração |
|---|---|
| `config/projects.php` (novo) | Fonte única de dados; lista vazia enquanto não houver projetos reais confirmados. |
| `app/Models/Project.php` (novo) | Carrega o arquivo PHP, oferece `all()` e `featured()`, normaliza opcionais, valida links HTTP(S) e caminhos de imagens locais existentes. |
| `app/Controllers/ProjectController.php` (novo) | Consulta todos os projetos e renderiza catálogo com título próprio e navegação ativa. |
| `app/Controllers/HomeController.php` | Consulta os projetos em destaque e os envia à home. |
| `public/index.php` | Instancia o Model compartilhado entre Controllers e registra GET `/projetos`. |
| `app/Core/Controller.php` | Disponibiliza URL do catálogo respeitando o caminho-base já existente. |
| `app/Views/projects/index.php` (novo) | Catálogo e aviso explícito quando não há itens. |
| `app/Views/partials/project-list.php` (novo) | Cards compartilhados: imagem/fallback, título, resumo, tecnologias e links disponíveis. |
| `app/Views/home/index.php` e `partials/header.php` | Substitui aviso futuro por destaques/estado vazio e chamada ao catálogo; menu aponta à nova rota e identifica página ativa. |
| `public/assets/css/style.css` | Grid fluido, cards, tecnologias, links e imagens 16:9 usando tokens existentes. |
| `public/assets/images/.gitkeep` (novo) | Preserva o diretório de imagens em novos checkouts. |
| `tests/run.php` | Amplia verificações de rota, catálogo, Model, destaques, opcionais e segurança com fixtures temporárias. |
| `README.md` e `PROJECT_CONTEXT.md` | Cadastro de projetos, regras dos opcionais, rotas, testes e checklist atualizado. |

### Decisões técnicas e limites de escopo

- Mantidos Router, layout, apresentação pessoal e tratamento de erro existentes. Models não geram HTML; Views recebem dados dos Controllers e não leem configuração. Nenhum framework, biblioteca, JavaScript ou banco de dados foi introduzido.
- Model carrega `config/projects.php` por padrão e aceita um caminho alternativo para testes. Controllers aceitam o Model por injeção; a entrada web compartilha uma instância por requisição. As consultas preservam a ordem do arquivo e apenas `featured === true` seleciona destaques.
- O arquivo real permanece vazio: não há dados de projetos confirmados. Exemplos existem apenas na documentação e em fixtures isoladas, sem atribuir projetos, tecnologias ou resultados ao proprietário. Catálogo vazio e ausência de destaques têm avisos próprios; botão de catálogo funciona em ambos os casos.
- Campos `image`, `github_url` e `demo_url` ausentes/nulos são tratados; tecnologias ausentes/nulas usam lista vazia e destaque omitido assume `false`. Dados obrigatórios devem seguir o formato documentado no README; não foi criada uma infraestrutura genérica de validação de configuração.
- Links aceitam somente URLs completas HTTP(S), sem credenciais; inválidos, vazios e esquemas como `javascript:`, `data:` e FTP são omitidos. Abrem na mesma aba e possuem nomes acessíveis com título do projeto. Todo texto e atributo dinâmico é escapado com `htmlspecialchars` e `ENT_QUOTES`.
- Imagens aceitam caminhos relativos sob `assets/images/`, sem travessia de diretórios, para arquivos existentes e extensões documentadas no README. Ausência ou caminho inválido recebe fallback sem gerar request quebrado. Imagens têm dimensões, proporção 16:9, texto alternativo e lazy loading.
- Cards são listas sem IDs derivados dos dados, compartilhadas entre catálogo e home; headings seguem h1/h2/h3. Grid se adapta ao espaço disponível mantendo cores, fontes e foco existentes. Revisão visual completa permanece pendente.
- Não foram adicionadas páginas individuais, consulta por slug ou links para `/projetos/{slug}`. Essas rotas continuam 404 e pertencem à etapa 5. Habilidades, contatos e SEO completo permanecem nas etapas próprias. Nenhuma alteração no Apache, permissões ou proprietário.

### Testes e evidências desta entrega

| Verificação | Resultado |
|---|---|
| `php tests/run.php` antes das mudanças | **186/186** aprovadas. |
| `php tests/run.php` após as mudanças | **320/320** aprovadas. |
| Sintaxe em `app`, `public`, `config`, `tests` e `index.php` | **18 arquivos PHP**, sem erros. |
| `git diff --check` | Aprovado. |
| Fixtures do Model e renderização | Inclusão de item apenas editando arquivo, todos os itens no catálogo, somente `featured` na home, catálogo vazio/sem destaques, opcionais ausentes/nulos, URLs válidas/inválidas, fallback de imagem e escaping aprovados. |
| Renderização isolada na raiz, `/portifolio` e subdiretório aninhado | Layout único, navegação, imagens e chamadas respeitam o caminho-base. |
| Apache `/portifolio` e PHP embutido com `APP_BASE_PATH=''` | Home **200**, catálogo **200**, catálogo com barra final/query **200**, rota inexistente **404**, `/projetos/exemplo` **404**, POST ao catálogo **404**. |
| CSS nos dois servidores | **200**, conteúdo idêntico ao arquivo atualizado; Apache com `text/css`. |
| Apache: acesso direto a `config/projects.php` e `app/Views/partials/project-list.php` | **403**, bloqueio privado preservado. |
| HTML servido nos dois servidores | Sem código PHP exposto; documento, landmarks e h1 únicos; IDs únicos, `aria-labelledby` válido, links e âncoras existentes e página ativa correta. |

A integração HTTP utilizou o autoload Composer existente e carregou as novas classes sem mudanças de dependências. O servidor PHP temporário foi encerrado após os testes e não registrou erros de aplicação. Os dados e a imagem de teste são criados temporariamente e removidos pela suíte; `config/projects.php` real não é alterado. Testes dos estados vazios usam Model isolado para continuarem válidos após cadastrar projetos reais.

A primeira tentativa de conexão local falhou no sandbox. As verificações HTTP passaram após escalada para acesso à rede local e abertura da porta temporária, sem mudar a configuração do servidor.

### Aceite, pendências e próxima retomada

- **Aceite funcional da etapa 4 atendido:** alterar o arquivo de dados inclui itens sem editar Views; cards e links opcionais são tratados; home seleciona destaques. O fluxo foi demonstrado com fixtures porque ainda não existem projetos reais confirmados.
- **Conteúdo:** obter lista real de projetos, títulos, resumos, descrições, tecnologias, imagens, repositórios/demos e seleção de destaques. Nome, biografia, formação e objetivos continuam pendentes das etapas anteriores.
- **Validação manual:** conferir cards vazios/preenchidos, grid mobile/desktop, zoom, foco visível, Tab/Enter nos links e console/requests de imagens. O inventário retornou `apps: []` e `browsers: []`; inspeção visual, responsividade real, console e teclado não puderam ser executados. As verificações de HTML/HTTP não substituem esses testes.
- Próxima implementação: **etapa 5 — páginas individuais dos projetos**, somente após novo pedido. Não antecipar a rota dinâmica ou `findBySlug()` antes dessa autorização.

## Histórico — etapa 3: página inicial e apresentação pessoal

Implementada exclusivamente a etapa 3, a pedido do proprietário. Etapas 1 a 3 concluídas funcionalmente; **etapa 4 não iniciada**. Árvore de trabalho limpa no início, no commit `6f65e6f` (`Implementa layout base e identidade visual da etapa 2`). Após a aprovação do proprietário, a entrega foi reunida no commit `Implementa página inicial e apresentação da etapa 3`; consultar `git log` para o hash. Não houve push.

### Alterações realizadas

| Arquivo | Alteração |
|---|---|
| `app/Views/home/index.php` | Substitui a view de teste por hero com título profissional confirmado, nome placeholder e chamadas; seções sobre mim, objetivos e aviso de projetos em preparação. |
| `app/Views/partials/header.php` | Links para sobre mim, objetivos e projetos usando a URL da home e respeitando o caminho-base, inclusive no 404. |
| `public/assets/css/style.css` | Estilos da apresentação e seções usando os tokens existentes; botões com quebra de linha e seções em duas colunas a partir de `48rem`. |
| `tests/run.php` | Atualiza a validação da home e verifica placeholders, chamadas, IDs únicos e destinos reais das âncoras locais e entre páginas. |
| `README.md`, `PROJECT_CONTEXT.md` e `PROGRESS.md` | Documenta funcionamento, limites, verificações e pendências; marca a etapa 3 no checklist. |

### Decisões técnicas e limites de escopo

- Mantidos `HomeController`, Controllers de erro, Core, Router, front controller, configuração e layout compartilhado. A apresentação está na View e os estilos no asset público existente; não há lógica de negócio nem leitura de dados na View.
- Sem novos Models, arquivos de dados, dependências ou JavaScript: os textos pessoais ausentes são placeholders estáticos. O título profissional Desenvolvedor Full Stack é a informação confirmada utilizada. Nome, biografia, formação, interesses e objetivos permanecem claramente sinalizados como pendentes; nenhuma experiência ou competência foi inventada.
- A chamada principal usa `#projetos`, que contém somente um aviso de preparação. Não aponta para uma rota inexistente; não implementa catálogo, cards, itens fictícios ou destaques. A rota `/projetos` continua retornando 404, para implementação na etapa 4.
- Header com links para `/#sobre`, `/#objetivos` e `/#projetos`, precedidos do caminho-base da instalação; chamadas dentro da home usam fragmentos locais. O retorno do 404 às seções funciona sem JavaScript.
- Seções identificadas por headings e `aria-labelledby`, com `tabindex="-1"` para receber foco ao navegar por fragmentos, sem entrar no percurso normal de Tab. Mantidos foco visível, link de salto e botões da etapa 2.
- Conteúdo em uma coluna nas telas pequenas e duas colunas nas seções a partir do breakpoint existente. Tema e tokens da etapa 2 preservados, sem downloads de fontes, fotos ou ilustrações.
- Habilidades, contatos, redes, currículo e revisão completa de SEO/acessibilidade permanecem nas etapas previstas. Nenhuma alteração de servidor, permissão ou proprietário.

### Testes e evidências desta entrega

| Verificação | Resultado |
|---|---|
| `php tests/run.php` antes das mudanças | **141/141** aprovadas. |
| `php tests/run.php` após as mudanças | **186/186** aprovadas. |
| Sintaxe de `app`, `public`, `config`, `tests` e `index.php` | **13 arquivos PHP**, sem erros. |
| `git diff --check` | Aprovado. |
| Apache: home `/portifolio/`, rota inexistente e CSS | **200**, **404** e **200**; HTML executado e CSS atualizado. |
| Servidor PHP, `APP_BASE_PATH=''`, raiz `public/` | Home **200**, rota inexistente **404**, CSS **200**. |
| `/projetos` em ambos os servidores | **404**, preservando o limite da etapa 3. |
| Apache: acesso direto a `/portifolio/app/Views/home/index.php` | **403**, proteção da View preservada. |
| Inspeção do HTML servido na home e páginas 404 | Documento e landmarks únicos, um h1, hierarquia de títulos válida, IDs únicos, `aria-labelledby` com destinos existentes e links/âncoras válidos, incluindo retorno do erro às seções da home. |

Os requests do servidor PHP temporário não registraram erros de aplicação. O servidor foi encerrado após a validação. A integração HTTP utiliza o autoload Composer existente, sem alterações. Abertura de porta e acesso HTTP local exigiram escalada do sandbox, como na etapa anterior.

### Pendências e próxima retomada

- **Conteúdo:** obter nome de exibição, biografia, formação, interesses e objetivos para substituir os placeholders. Se esses textos passarem a ser dados dinâmicos, continuar a aplicar escaping no contexto HTML.
- **Validação manual:** inspecionar mobile/desktop e zoom, verificar console e usar Tab/Enter no menu, link de salto, chamadas e retorno do 404. O inventário novamente retornou `apps: []` e `browsers: []`; renderização, rolagem e interação real por teclado não puderam ser verificadas. As evidências de HTML/HTTP não substituem esses testes.
- Aceite funcional da etapa 3 atendido com placeholders explícitos e chamadas com destinos válidos. Validação visual e substituição dos dados pessoais continuam pendentes conforme acima.
- Próxima implementação: **etapa 4 — catálogo de projetos**, somente após novo pedido. Ao retomá-la, substituir o aviso em `#projetos` pela integração de destaques prevista no roadmap e adicionar o link ao catálogo quando a rota existir.

## Histórico — etapa 2: layout base e identidade visual

Implementada exclusivamente a etapa 2, por solicitação explícita do proprietário. Etapas 1 e 2 concluídas; **etapa 3 ainda não iniciada**. O repositório estava limpo no início desta implementação, no commit `7b51b09` (`Document visual reference and Full Stack positioning`). Após a aprovação do proprietário, a entrega foi reunida no commit `Implementa layout base e identidade visual da etapa 2`; consultar `git log` para o hash. Não foi realizado push.

### Alterações e responsabilidades

| Arquivo ou área | Alteração |
|---|---|
| `app/Core/Controller.php` | Recebe configuração comum; prepara nome, título, caminho da home e CSS; renderiza a view dentro do layout. Mantém a verificação de existência da view e `EXTR_SKIP`. |
| `app/Controllers/HomeController.php` | Usa configuração herdada e informa a página atual; conteúdo de teste preservado. |
| `app/Controllers/ErrorController.php` e `public/index.php` | Configuração passada também ao erro, para usar layout e links com o mesmo caminho-base; status 404 preservado. |
| `app/Views/layouts/main.php` (novo) | Documento HTML único, título e stylesheet escapados, link para pular navegação e landmark principal com destino focável. |
| `app/Views/partials/header.php` (novo) | Marca textual placeholder, navegação semântica e `aria-current` somente na home. |
| `app/Views/partials/footer.php` (novo) | Posicionamento confirmado de Desenvolvedor Full Stack e retorno ao topo. |
| `app/Views/home/index.php` e `app/Views/errors/404.php` | Apenas conteúdo, sem duplicar documento/layout. Home sinalizada como em construção; 404 com botão para voltar à home. |
| `public/assets/css/style.css` | Tema escuro, variáveis de cores/espaçamento, tipografia, container fluido, botões primário/secundário, foco visível e estilos para telas pequenas e grandes. |
| `tests/run.php` | Verificações ampliadas para layout único, escaping do header/links, navegação e 404 na raiz e em subdiretórios. |
| `README.md` e `PROJECT_CONTEXT.md` | Uso do layout, decisões visuais, evidências e limitações documentados; checklist da etapa 2 atualizado. |

### Decisões técnicas

- Layout fixo compartilhado, com partials PHP e inclusão direta da view pelo caminho interno já validado pelo Controller; sem engine de templates ou novas dependências. Models e Router não foram alterados.
- Configuração injetada no Controller base, evitando repetir URLs de assets e navegação nos Controllers específicos. O front controller continua responsável por carregar a configuração.
- Escolha visual reversível: fundo `#101014`, superfície `#18181f`, texto `#f4f4f7`, texto secundário `#b4b4c2`, destaque `#b69aff` e fontes do sistema. As referências [site iuricode](https://www.iuricode.com/) e [repositório](https://github.com/iuricode/portfolio) foram consultadas como orientação; não foram incorporados conteúdo pessoal, imagens, marca ou código de terceiros. A aprovação dos detalhes visuais pelo proprietário continua pendente.
- Escala de espaçamento de `0.5rem` a `5rem`, container máximo de `72rem`, títulos fluidos e breakpoint em `48rem`; header/footer com quebra de linha e footer ao fim da tela por flexbox, sem posição fixa.
- Navegação apenas para destinos já disponíveis: home, conteúdo e topo; retorno do 404 à home. Não adicionar `/projetos`, habilidades, contato ou âncoras vazias antes das respectivas etapas.
- Sem JavaScript ou dependências de fontes/imagens externas. Foco visível, link de salto, áreas de link com altura mínima de `2.75rem` e identificação semântica da navegação fazem parte desta base; a revisão completa de acessibilidade permanece na etapa 8.
- Nome mantido como `Meu portfólio`; nenhuma biografia, experiência, habilidade, projeto ou contato foi inventado. A home continua sendo a view de teste, sem implementar hero ou seções da etapa 3.

### Verificações executadas nesta entrega

| Verificação | Resultado |
|---|---|
| Suite antes das mudanças | **43/43** aprovada. |
| `php tests/run.php` após as mudanças | **141/141** aprovada. |
| `php -l` em `app`, `public`, `config`, `tests` e `index.php` | **13 arquivos**, sem erros. |
| `git diff --check` | Aprovado. |
| Apache: `/portifolio/` | **200**, corpo HTML executado, sem código-fonte PHP. |
| Apache: `/portifolio/rota-inexistente` | **404**, página com layout compartilhado. |
| Apache: `/portifolio/assets/css/style.css` | **200**, `text/css`, conteúdo idêntico ao CSS atualizado. |
| Apache: `/portifolio/config/app.php` e `/portifolio/app/Views/partials/header.php` | **403**, bloqueio privado preservado. |
| PHP embutido, `APP_BASE_PATH=''`, raiz `public/` | Home **200**, rota inexistente **404**, CSS **200**. |
| Inspeção dos corpos HTML nos dois servidores | Documento, header/main/footer e h1 únicos; destinos de links e âncoras válidos; `aria-current` apenas na home. |

As verificações HTTP confirmaram a integração com o autoload Composer existente. Não houve alteração de dependências ou autoload; não foi necessário reinstalar Composer. Os requests do servidor PHP não registraram erros de aplicação. O servidor temporário foi encerrado após a validação.

O acesso HTTP local e a abertura da porta exigiram escalada do sandbox; a primeira tentativa isolada não conseguiu conectar/escutar. As verificações acima passaram após a escalada. Nenhuma configuração do Apache, permissão ou propriedade foi alterada.

### Aceite, limitações e próxima retomada

- Critérios da etapa 2 atendidos na estrutura e nas verificações funcionais: páginas existentes reutilizam layout sem duplicar documento HTML, navegação aponta para destinos válidos e estilos estão preparados para responsividade.
- **Pendente de verificação manual:** renderização visual em mobile/desktop, zoom, foco e ativação dos links por teclado, comportamento do salto ao conteúdo/topo e console/ausência de assets quebrados no navegador. A ferramenta retornou `apps: []` e `browsers: []`; não foi possível produzir screenshots ou validar interações reais. A inspeção de HTML e HTTP não substitui esses testes.
- **Pendente de conteúdo/validação:** nome definitivo e confirmação da paleta/tipografia; biografia, projetos, habilidades e contatos continuam reservados às etapas futuras.
- Na próxima sessão, ler os documentos, conferir `git status` e validar visualmente home e 404 quando houver navegador disponível. A próxima etapa de implementação é a **etapa 3 — página inicial e apresentação pessoal**, somente mediante novo pedido.

---

Histórico abaixo: entrega da etapa 1, anterior à implementação descrita na seção 0.

## 1. Estado de entrega e limite de escopo

- Etapa 1 — estrutura inicial e configuração MVC — implementada e validada com o servidor embutido do PHP.
- Checkbox da etapa 1 marcado em `PROJECT_CONTEXT.md` com base nessa validação funcional.
- Correção da integração do Apache com PHP confirmada pelo proprietário após executar a correção orientada. Não houve nova verificação automatizada das rotas no Apache após essa confirmação.
- Etapas 2 a 9 não foram iniciadas. **Não avançar para a etapa 2 sem novo pedido explícito.**
- Não foram inventados dados pessoais, habilidades, projetos ou contatos.
- Não foram adicionados framework, banco de dados, layout compartilhado ou funcionalidades futuras.

## 2. Documentos e regras para retomar

Ler `AGENTS.md`, `PROJECT_CONTEXT.md`, este arquivo e `README.md` antes de alterar o projeto em `/var/www/html/portifolio`.

- `PROJECT_CONTEXT.md` contém o roadmap, os critérios de aceite e as decisões pendentes.
- `README.md` documenta execução, testes e a validação realizada.
- Preservar a separação MVC e limitar mudanças à etapa autorizada.
- Operações normais no repositório devem ocorrer como `heitor`, sem mudar permissões ou proprietário.
- Evitar mudanças no Apache durante edições comuns de conteúdo. A correção do ambiente foi investigada após o usuário relatar a exposição de PHP no navegador, mas exigiu privilégio administrativo indisponível.
- Não solicitar nem registrar senhas. Caso seja necessário `sudo` interativo, o proprietário deve executá-lo no próprio terminal.

## 3. Origem e aplicação da etapa 1

A implementação havia sido preparada anteriormente, mas a gravação no repositório fora bloqueada pelo sandbox daquela sessão.

Artefatos de referência externos ao repositório:

- `/home/heitor/portfolio-etapa1-relatorio.md`: relatório anterior, lido nesta sessão.
- `/home/heitor/portfolio-etapa1.patch`: patch lido, conferido e aplicado nesta sessão.
- `/home/heitor/portfolio-etapa1-preparacao`: cópia de preparação mencionada no relatório; não é a árvore de trabalho atual.

O estado original foi inspecionado e `git apply --check` passou. O patch foi aplicado sem conflitos. **Não reaplicar o patch**: suas alterações já estão incorporadas ao repositório e ao commit descrito abaixo.

Após a aplicação, foram acrescentados `composer.lock`, o registro de validação no README e a marcação da etapa 1 no contexto. A checagem reversa do patch passou para os arquivos de implementação; README recebeu conteúdo adicional e, por isso, não corresponde mais literalmente ao patch original.

## 4. Implementação atual

| Arquivo ou área | Responsabilidade e estado |
|---|---|
| `composer.json` | Projeto PHP `^8.2`, PSR-4 `App\\` → `app/`; nenhuma biblioteca externa. |
| `composer.lock` | Gerado e versionado; nenhuma dependência de biblioteca. |
| `vendor/` | Autoload Composer gerado localmente; ignorado pelo Git. Deve ser recriado por Composer em outro checkout. |
| `public/index.php` | Front controller: carrega autoload e configuração, registra a home, despacha e trata 404; exceções retornam 500 genérico, detalhes no log PHP. |
| `index.php` | Entrada de compatibilidade na raiz que inclui `public/index.php`. |
| `config/app.php` | Nome placeholder `Meu portfólio` e caminho-base definido por `APP_BASE_PATH`. |
| `app/Core/Router.php` | Rotas GET com handlers callable; `dispatch()` retorna bool, remove query string, normaliza barra final e respeita os limites do prefixo da instalação. |
| `app/Core/Controller.php` | Renderiza views com dados; não concentra HTML ou regras específicas de negócio. |
| `app/Controllers/HomeController.php` | Recebe configuração e passa título e URL do CSS para a view de teste. |
| `app/Controllers/ErrorController.php` | Define status 404 e renderiza a view de erro. |
| `app/Views/home/index.php` | HTML de teste, título e atributo do CSS escapados com `htmlspecialchars` e `ENT_QUOTES`. |
| `app/Views/errors/404.php` | Página simples de recurso inexistente, sem caminhos internos. |
| `public/assets/css/style.css` | Estilo mínimo existente, preservado; não representa a implementação da etapa 2. |
| `.htaccess` | Reescrita para `public/` e bloqueio de diretórios privados/metadados; contém caminhos específicos de `/portifolio`. |
| `public/.htaccess` | Encaminha requisições não correspondentes a arquivo/diretório para o front controller. |
| `tests/run.php` | 43 verificações isoladas de roteamento, renderização, escaping, 404 e configuração. |
| `.gitignore` | Ignora `vendor/`, `.env` e logs, preservando `storage/logs/.gitkeep`. |

O autoloader manual `app/Core/Autoloader.php` foi removido pelo patch. Não recriá-lo como substituto do Composer.

### Caminho-base

- Padrão: `/portifolio`, para `http://localhost/portifolio/` no Apache.
- Para servidor com document root em `public/`, usar `APP_BASE_PATH=''`.
- O arquivo de configuração normaliza o valor vazio para `/`; os consumidores removem a barra final, produzindo o prefixo efetivamente vazio.
- Para produção, o plano prevê document root em `public/`; nenhum deploy foi realizado.

## 5. Composer e ambiente de execução

- Ambiente inspecionado: Debian 13 (trixie), PHP CLI 8.4.26 e Apache 2.4.69.
- Não havia comando `composer` instalado no PATH.
- Instalador oficial obtido de `getcomposer.org`; SHA-384 conferido com a assinatura publicada em `composer.github.io/installer.sig`.
- Composer 2.10.3 instalado temporariamente em `/tmp/portfolio-composer.phar`.
- Instalador e assinatura temporários: `/tmp/portfolio-composer-setup.php` e `/tmp/portfolio-composer-installer.sig`.
- Esses arquivos em `/tmp` podem desaparecer entre sessões. Não pressupor instalação global.
- A primeira execução do Composer encontrou restrição de DNS e cache no sandbox, mas gerou lock e autoload. A instalação foi repetida fora do isolamento de rede e passou.
- Na repetição foram usados `COMPOSER_HOME=/tmp/portfolio-composer-home` e `COMPOSER_CACHE_DIR=/tmp/portfolio-composer-cache`.
- A extensão PHP curl não está habilitada no CLI; o Composer avisou sobre desempenho. Isso não impediu as verificações.
- `composer validate` avisou somente sobre ausência de licença. O proprietário ainda não escolheu uma licença; não inventar uma.

## 6. Verificações já executadas

| Verificação | Resultado |
|---|---|
| Aplicabilidade do patch antes da alteração | Aprovada. |
| Composer install | Aprovado; lock e autoload gerados. |
| Composer validate `--no-check-publish` | Válido, com aviso de licença ausente. |
| Composer dump-autoload `--optimize --strict-psr` | Aprovado; 5 classes no autoload otimizado. |
| `php tests/run.php` | **43/43** verificações aprovadas. |
| Sintaxe de PHP em `app`, `public`, `config`, `tests` e `index.php` | **10 arquivos**, sem erros. |
| GET `http://127.0.0.1:8000/` | **200**, HTML da view de teste e URL correta do CSS. |
| GET `http://127.0.0.1:8000/rota-inexistente` | **404**, view própria de erro. |
| GET `http://127.0.0.1:8000/assets/css/style.css` | **200**, conteúdo CSS. |
| `git diff --cached --check` antes do commit | Aprovado. |

Os testes isolados carregam classes diretamente. A verificação HTTP pelo servidor PHP foi realizada separadamente para confirmar a integração real do autoload Composer.

O relatório anterior também registrava a resposta 500 genérica sem autoload em CLI; essa evidência é da preparação anterior, não de uma nova simulação nesta sessão.

O servidor PHP de teste foi iniciado com `APP_BASE_PATH='' php -S 127.0.0.1:8000 -t public` e **encerrado após a validação**. Não pressupor que esteja rodando.

Renderização visual, responsividade e console não foram validados: a ferramenta de navegador retornou inventário vazio. A view atual não possui JavaScript nem elementos interativos para teste por teclado.

## 7. Problema do Apache resolvido pelo proprietário

**Estado atual:** o proprietário confirmou que a correção do Apache funcionou. A exposição do código PHP deixou de ser uma pendência reportada. O diagnóstico e as tentativas abaixo são histórico; não repetir a instalação sem conferir o ambiente. A confirmação é do proprietário, sem nova medição HTTP pela ferramenta após a correção.

O usuário reproduziu no navegador o mesmo problema observado durante a validação: `http://localhost/portifolio/` mostra o conteúdo de `public/index.php`, incluindo `<?php`, em vez de executar a aplicação. O texto com acentos corrompidos nessa resposta não é evidência de problema na codificação dos templates; o PHP nem sequer está sendo executado.

Evidências HTTP no Apache antes da correção:

- Home: **200**, corpo contendo código-fonte PHP.
- Rota inexistente: **200**, também contendo o código-fonte do front controller; não produz o 404 da aplicação.
- `/portifolio/assets/css/style.css`: **200**.
- `/portifolio/config/app.php`: **403**, mostrando que o bloqueio correspondente no `.htaccess` funciona.

Diagnóstico confirmado por inspeção:

- Apache com `mpm_event` e `rewrite` habilitados.
- Nenhum módulo PHP habilitado no Apache.
- Pacotes `libapache2-mod-php*` e `php*-fpm` não instalados na inspeção.
- `/run/php` inexistente.
- Apenas a configuração PHP CLI foi encontrada para a versão inspecionada.
- `php8.4-fpm` disponível no APT, candidato `8.4.26-1~deb13u1` na ocasião.
- Site Apache padrão com document root `/var/www/html`.
- `/etc/apache2/conf-available/portifolio-htaccess.conf` permite `AllowOverride All` no diretório do projeto.

Foi tentado `sudo -n apt-get install -y php8.4-fpm` com execução escalada. A tentativa terminou com **`sudo: a password is required`**. Não houve instalação confirmada nem alteração de configuração do Apache nesta sessão. Esse bloqueio foi de autenticação administrativa, não uma rejeição automática da ação.

O usuário recebeu a sequência abaixo para executar no próprio terminal e posteriormente confirmou que a correção deu certo:

```bash
sudo apt-get install php8.4-fpm &&
sudo a2enmod proxy_fcgi setenvif &&
sudo a2enconf php8.4-fpm &&
sudo service php8.4-fpm start &&
sudo apache2ctl configtest &&
sudo service apache2 reload
```

A escolha de FPM preserva o modelo `mpm_event` existente. Conferir o estado atual antes de executar qualquer correção: o proprietário pode ter realizado esses comandos fora da sessão.

A correção ocorreu no ambiente do servidor; não exigiu alterar a lógica MVC ou adicionar um autoloader alternativo. A confirmação de funcionamento local não representa validação de publicação em produção.

## 8. Git

- Branch atual na última inspeção: `master`.
- Foi criado o primeiro commit do repositório, a pedido do usuário:
  - Hash: `25a08fc9f74296e296796314dec604954d457c78`.
  - Mensagem: `Configura estrutura MVC e valida etapa 1`.
  - 21 arquivos, 805 inserções; inclui implementação, configuração, testes e documentos existentes.
- `vendor/` não foi versionado; `composer.lock` foi incluído.
- O diretório de trabalho estava limpo após o commit e antes da criação deste documento.
- A escrita em `.git/index.lock` foi bloqueada no sandbox. A operação Git foi repetida com escalada e passou, sem mudar permissões ou proprietário.
- Não foi realizado push nesta sessão.
- `PROGRESS.md` foi criado depois do commit inicial. O proprietário solicitou seu versionamento junto da atualização do README que registra a resolução do Apache. Consultar `git log` para o commit dessa documentação.

## 9. Retomada recomendada

1. Ler os documentos e conferir `git status`, o último commit e o estado atual do Apache/PHP-FPM.
2. Considerar o problema de execução PHP no Apache resolvido conforme confirmação do proprietário; não repetir os comandos administrativos automaticamente.
3. Na próxima revisão de integração, validar no Apache home 200 com HTML executado, rota inexistente 404, CSS 200 e bloqueio dos arquivos privados. Esses resultados específicos ainda não foram medidos pela ferramenta após a correção. Um status 200 sozinho não comprova execução PHP: conferir o corpo.
4. Abrir no navegador, quando disponível, e conferir renderização, carregamento do CSS e ausência de exposição de código-fonte.
5. Atualizar README e este registro com os resultados efetivamente observados.
6. Não iniciar a etapa 2 sem autorização explícita. A próxima etapa prevista é layout base e identidade visual; dados pessoais e identidade visual continuam pendentes no contexto do projeto.

### Comandos locais de referência

```bash
cd /var/www/html/portifolio
php /tmp/portfolio-composer.phar install --no-interaction
php /tmp/portfolio-composer.phar validate --no-check-publish
php /tmp/portfolio-composer.phar dump-autoload --optimize --strict-psr
php tests/run.php
find app public config tests -name '*.php' -print0 | xargs -0 -n1 php -l
php -l index.php
```

Se o PHAR temporário não existir, usar uma instalação Composer oficial disponível ou reinstalá-lo com verificação de integridade. Para testar sem depender do Apache:

```bash
APP_BASE_PATH='' php -S 127.0.0.1:8000 -t public
```

Em outro terminal:

```bash
curl -i http://127.0.0.1:8000/
curl -i http://127.0.0.1:8000/rota-inexistente
curl -i http://127.0.0.1:8000/assets/css/style.css
```

No ambiente de ferramentas desta sessão, acesso à rede local e abertura de porta exigiram escalada do sandbox. Isso é separado das permissões normais de `heitor` sobre os arquivos do projeto.
