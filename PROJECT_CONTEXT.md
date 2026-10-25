# Contexto do Projeto — Portfólio Pessoal PHP MVC

> Documento de referência para desenvolvimento incremental com Codex. Deve ser consultado antes de implementar qualquer etapa.

> **Exportação estática — 22/10/2026:** etapas 1–5 de `STATIC_DEPLOY_PLAN.md` implementadas, com prioridade para Cloudflare Pages. PHP MVC preservado como fonte; exportador CLI reutiliza `public/index.php`, gera seis páginas públicas + 404, sitemap/robots e copia somente sete assets referenciados. `portfolio-mvc-php` continua oculto. URL de página com barra final é aplicada somente na exportação; PHP web preserva comportamento mesmo com a flag no ambiente. DOM é requisito apenas das ferramentas de build. Corrigida a expectativa antiga do teste de currículo isolado, mantendo apresentação e PDF. Resultados atuais: 1.601 PHP, 135 de exportação, 139 JS, 172 HTTP/navegador e 32 comparações visuais idênticas; 36 PHP sem erro de sintaxe. Leia `README.md` e `STATIC_EXPORT_VALIDATION.md` para comandos e limites. A saída local usa origem ilustrativa `https://portfolio.example`; reconstruir para o endereço final antes de publicar. Não há configuração Cloudflare, workflow, deploy, git add, commit ou push nesta etapa. Os registros de redesign/MVP abaixo são históricos.

> **Redesign — 21/10/2026:** etapas 1–8 confirmadas pelo responsável; etapa 9 executada e validada localmente, com aceite visual final e Lighthouse pendentes. Chrome instalado usado por CLI/DevTools com perfis temporários, permitindo capturas reais e verificações de layout, teclado, contraste, temas, rede e movimento. Corrigida a atualização das preferências das partículas fora do ciclo de desenho. PHP MVC, Models, rotas, dependências e cinco configurações preservados integralmente nesta etapa. E-mail autorizado `heitorsvareiro@gmail.com` mantém rótulo “E-mail de contato”; currículo `CurriculoLinkedin.pdf` fornecido pelo responsável e disponível para download em Contato. Resultados: 1.601 PHP, 139 JavaScript, 480 verificações de matriz no Chrome, 39 de interação e seis de partículas aprovadas; HTTP em ambos os caminhos-base. Relatório, 30 capturas e limites em `design/REDESIGN_VALIDATION.md`. LinkedIn bloqueou a consulta automatizada; endereço mantido. Servidores de teste e perfis temporários encerrados. Não publicar, habilitar o projeto oculto ou criar commits sem solicitação. O roadmap do MVP abaixo é histórico e independente deste redesign.

## 1. Visão geral

Atualização após a revisão final: retrato fornecido pelo responsável em `public/assets/images/meu-retrato.png` incluído no avatar circular da home, com texto alternativo e zoom de 15% por CSS. O favicon `public/assets/images/favicon.svg` é usado em todas as páginas. Validado nos dois temas em 390/1440px; evidências atuais em `design/validation/portrait-favicon/`. As capturas e métricas da etapa 9 registram o estado anterior à inclusão do retrato.

Desenvolver um **site de portfólio pessoal** para apresentar o desenvolvedor, seus projetos, habilidades e meios de contato. O endereço será divulgado no **LinkedIn, GitHub e currículo**. A experiência deve ser profissional, acessível, responsiva, rápida e simples de manter.

O site é o portfólio pessoal de **Heitor Silva Vareiro**, com posicionamento de **Desenvolvedor Web Full Stack** e condição de estudante ativo de Técnico Integrado em Informática no IFMS. Perfil, projetos e habilidades foram preenchidos em 19/10/2026 conforme `PORTFOLIO_CONTENT_GUIDE_ATUALIZADO.md`, currículo e cópias locais dos repositórios ativos. Não atribuir competências ou resultados além dessas fontes.

### Referência de identidade visual

A identidade visual terá como inspiração o portfólio de Iuri Silva (iuricode), disponível nas seguintes referências:

- **Repositório no GitHub:** [iuricode/portfolio](https://github.com/iuricode/portfolio).
- **Site de referência:** [iuricode.com](https://www.iuricode.com/).

Essas referências devem orientar as decisões visuais do layout, adaptadas à identidade pessoal do proprietário e ao posicionamento de Desenvolvedor Full Stack. Os textos, projetos, habilidades e dados profissionais da referência não representam o proprietário e não devem ser incorporados como conteúdo pessoal. A paleta, a tipografia e os demais detalhes visuais serão definidos durante a etapa de layout com base nessa inspiração.

**Objetivo do MVP:** disponibilizar um site público que apresente uma introdução pessoal, uma lista de projetos e páginas individuais de cada projeto, habilidades técnicas e links de contato.

**Público-alvo:** recrutadores, equipes técnicas, outros desenvolvedores e pessoas interessadas nos projetos.

### Fora do escopo inicial

- Login, autenticação e painel administrativo.
- Banco de dados e operações CRUD via interface.
- Formulário de contato com envio de e-mail.
- Blog, comentários, avaliações e analytics.
- Internacionalização e alternância de tema (possíveis evoluções).

## 2. Stack e decisões arquiteturais

| Área | Escolha |
|---|---|
| Back-end | PHP 8.2 ou superior |
| Arquitetura | MVC, sem framework PHP completo |
| Front-end | HTML5, CSS3 e JavaScript (sem framework obrigatório) |
| Dependências | Composer para autoload PSR-4; outras apenas se justificadas |
| Dados dos projetos | Arquivo PHP de configuração no MVP (`config/projects.php`) |
| Banco de dados | Não utilizar no MVP; MySQL pode ser integrado futuramente |
| Controle de versão | Git e GitHub |
| Ambiente de produção | Hospedagem com PHP e document root apontando para `public/` |

### Responsabilidades MVC

- **Model:** representa e consulta os dados dos projetos e demais entidades; não gera HTML.
- **View:** apresenta os dados, utiliza templates/partials, escapa saída dinâmica; não realiza lógica de negócio.
- **Controller:** recebe os parâmetros de rota, solicita dados ao Model e escolhe as Views/respostas.
- **Router/Core:** faz despacho de rotas, tratamento de 404 e mecanismos reutilizáveis, sem concentrar funcionalidades específicas do portfólio.

**Princípios:** clareza antes de abstração, baixo acoplamento, responsabilidade única e ausência de complexidade desnecessária. Evitar criação de infraestrutura genérica sem uso concreto.

## 3. Estrutura proposta

```text
portfolio/
├── app/
│   ├── Controllers/
│   │   ├── HomeController.php
│   │   └── ProjectController.php
│   ├── Models/
│   │   └── Project.php
│   ├── Core/
│   │   ├── Router.php
│   │   └── Controller.php
│   └── Views/
│       ├── layouts/
│       ├── partials/
│       ├── home/
│       ├── projects/
│       └── errors/
├── config/
│   ├── app.php
│   └── projects.php
├── public/
│   ├── assets/
│   │   ├── css/
│   │   ├── js/
│   │   └── images/
│   └── index.php
├── tests/
├── composer.json
├── .gitignore
├── README.md
└── PROJECT_CONTEXT.md
```

A estrutura é uma proposta inicial, não autorização para criar todos os arquivos imediatamente. Cada arquivo deve surgir quando sua etapa exigir. Caso o repositório já contenha outra estrutura funcional, **adaptar o plano ao código existente** em vez de substituí-lo sem necessidade.

### Fluxo de requisição

1. Servidor direciona requisições ao `public/index.php` (front controller).
2. O Router reconhece a URL e o método HTTP.
3. Controller executa a ação correspondente.
4. Model acessa os dados quando necessário.
5. Controller passa os dados à View.
6. View renderiza o HTML com layout e partials reutilizáveis.
7. Rotas inexistentes retornam página de erro com status HTTP 404.

## 4. Páginas e rotas previstas

| Método | Rota | Finalidade | Responsável sugerido |
|---|---|---|---|
| GET | `/` | Página inicial e apresentação | `HomeController` |
| GET | `/projetos` | Catálogo completo de projetos | `ProjectController` |
| GET | `/projetos/{slug}` | Detalhe de um projeto | `ProjectController` |
| GET | qualquer rota inexistente | Resposta 404 | Router/erro |

A seção de habilidades e os meios de contato podem estar na página inicial, com âncoras (`/#habilidades`, `/#contato`), evitando páginas extras sem necessidade. Navegação e URLs devem ser consistentes; a configuração de reescrita depende do servidor escolhido.

## 5. Conteúdo funcional

### Página inicial

- Nome do desenvolvedor e uma apresentação breve.
- Título profissional: “Desenvolvedor Full Stack”.
- Resumo de interesses, formação e objetivos profissionais.
- Projetos em destaque e botão para catálogo completo.
- Habilidades agrupadas por categorias.
- Links para GitHub, LinkedIn e e-mail; currículo PDF opcional.

**Conteúdo confirmado em 19/10/2026:** perfil, formação em andamento (2024–2026, término previsto), estágio de Desenvolvimento Web no IFMS (março–outubro/2026) no Arco Literário, quatro certificações pelos nomes, habilidades, projetos, GitHub e LinkedIn. Dados pessoais em `config/profile.php`, consultados pelo Model `Profile`; os demais dados usam os Models existentes. E-mail depende de autorização específica e currículo PDF não foi fornecido. **Nunca inventar experiências, formação, competências ou resultados de projetos.**

### Catálogo de projetos

- Cards com imagem, título, resumo, tecnologias e links disponíveis.
- Layout responsivo.
- Projetos marcados como `featured` aparecem na seção de destaque da home.
- Projetos são mantidos em um arquivo de dados e não codificados diretamente nas Views.
- Um projeto sem demonstração ou imagem própria continua sendo exibido adequadamente.

### Detalhe de projeto

- URL amigável baseada em `slug` (ex.: `/projetos/crowns`).
- Título, descrição completa, imagens opcionais, tecnologias utilizadas, links válidos para repositório e demonstração.
- Retorno à listagem e 404 quando o slug não existir.

### Habilidades

Organizar em categorias como linguagens, back-end, front-end, banco de dados e ferramentas, exibindo **somente conhecimentos confirmados**. Referências iniciais a validar com o proprietário: Java, PHP, JavaScript, HTML, CSS, Bootstrap, Node.js/Express, PostgreSQL, MongoDB, Git e GitHub.

### Contato

Links diretos para GitHub e LinkedIn, link `mailto:` e arquivo de currículo caso fornecido. Não desenvolver formulário de contato no MVP.

## 6. Modelo de dados de projeto

Campos previstos:

| Campo | Tipo sugerido | Obrigatório | Observação |
|---|---|---|---|
| `id` | int/string | Sim | Identificador único |
| `slug` | string | Sim | Único; formato URL amigável |
| `title` | string | Sim | Nome do projeto |
| `summary` | string | Sim | Resumo para card |
| `description` | string | Sim | Descrição para página individual |
| `technologies` | array<string> | Sim | Lista de tecnologias, pode ser vazia |
| `image` | string/null | Não | Caminho público de imagem ou fallback |
| `github_url` | string/null | Não | URL pública válida |
| `demo_url` | string/null | Não | URL de demonstração válida |
| `featured` | bool | Sim | Define exibição na home |

Formato de referência **ilustrativo** para `config/projects.php`:

```php
<?php

return [
    [
        'id' => 1,
        'slug' => 'exemplo-de-projeto',
        'title' => 'Exemplo de Projeto',
        'summary' => 'Resumo curto do que o projeto faz.',
        'description' => 'Descrição detalhada do problema e da solução.',
        'technologies' => ['PHP', 'HTML', 'CSS'],
        'image' => null,
        'github_url' => null,
        'demo_url' => null,
        'featured' => true,
    ],
];
```

O exemplo acima é conteúdo fictício de desenvolvimento e **não deve ser apresentado como um projeto real na versão publicada**. Model deve oferecer operações como `all()`, `featured()` e `findBySlug(string $slug)` (nomes ajustáveis conforme código existente).

## 7. Requisitos não funcionais

### Segurança

- Escapar valores dinâmicos no contexto correto ao gerar HTML (por exemplo, com `htmlspecialchars` e `ENT_QUOTES`).
- Validar slugs e entradas de rota; retornar 404 quando um recurso não existir.
- Não interpretar conteúdo de descrição como HTML arbitrário sem política explícita de sanitização.
- Escapar atributos HTML e validar URLs antes de usá-las em links.
- Não divulgar stack traces, segredos, caminhos internos ou mensagens sensíveis em produção.
- Garantir que a raiz pública do servidor seja `public/`; impedir acesso web direto a `app/`, `config/` e arquivos privados.
- Dependências e arquivos de configuração devem ser revisados antes do deploy.

### Interface e acessibilidade

- Layout mobile-first e compatível com telas pequenas, médias e grandes.
- HTML semântico, heading hierarchy correta, foco visível e navegação por teclado.
- Texto alternativo adequado para imagens relevantes.
- Contraste e tamanho de fonte legíveis.
- Imagens otimizadas, dimensões definidas quando possível e carregamento eficiente.
- Identidade visual consistente; tema exato será definido com o proprietário.

### SEO e compartilhamento

- `title` e `meta description` específicos por página.
- Open Graph para previews de links compartilhados no LinkedIn.
- URLs legíveis e uso correto de status HTTP.
- `robots.txt`, `sitemap.xml` e URL canônica após definição do domínio.

### Manutenção e qualidade

- Nomes de código claros e consistentes.
- Sem duplicação importante de markup entre páginas.
- README com requisitos, instalação local, rotas, estrutura e como cadastrar projetos.
- Testes básicos de roteamento, consulta de projetos e renderização/erros, quando viáveis.

## 8. Roadmap incremental de implementação

**Regra de execução:** realizar **uma etapa por vez**. Não antecipar funcionalidades. Após cada etapa, verificar os critérios de aceite antes de prosseguir.

### Etapa 1 — Estrutura inicial e configuração MVC

**Tarefas:** organizar diretórios, criar front controller, configurar autoload Composer PSR-4, roteador GET, Controller base apenas se útil, configurações e tratamento 404.

**Aceite:** a aplicação inicializa; `/` responde com view de teste; rota desconhecida retorna status 404. É possível executar localmente seguindo documentação mínima.

### Etapa 2 — Layout base e identidade visual

**Tarefas:** definir estilos-base, criar layout compartilhado, header, navegação, footer e partials; estabelecer padrões de tipografia, cores, espaçamento e botões.

**Aceite:** todas as páginas existentes reutilizam layout sem HTML repetido; navegação funcional; estrutura preparada para responsividade.

### Etapa 3 — Página inicial e apresentação pessoal

**Tarefas:** criar hero, seção sobre mim, objetivos e links/chamadas para projetos. Usar placeholders explícitos quando conteúdo pessoal ainda não foi informado.

**Aceite:** home navegável, apresentação concisa, chamadas funcionais e ausência de dados biográficos inventados.

### Etapa 4 — Catálogo de projetos

**Tarefas:** definir arquivo de dados, Model e ProjectController; criar cards e listagem; destacar projetos marcados como `featured` na home; lidar com campos opcionais.

**Aceite:** incluir novo item no arquivo de dados o faz aparecer na listagem sem editar Views; links e campos opcionais não quebram o layout.

### Etapa 5 — Páginas individuais de projetos

**Tarefas:** implementar rota GET `/projetos/{slug}`, consulta por slug, página de detalhes e tratamento de projetos inexistentes.

**Aceite:** cada projeto possui URL compartilhável; páginas mostram os dados corretos; slug desconhecido retorna 404.

### Etapa 6 — Habilidades e tecnologias

**Tarefas:** organizar habilidades em categorias e criar seção reutilizável; não atribuir conhecimentos não confirmados ao proprietário.

**Aceite:** seção legível, consistente e atualizável sem reescrever o layout.

### Etapa 7 — Contato e redes profissionais

**Tarefas:** inserir links confirmados para LinkedIn, GitHub e e-mail; currículo apenas se disponível; verificar `target`/`rel` em links externos conforme necessidade.

**Aceite:** links e download de currículo (quando habilitado) funcionam; não há formulário de contato no MVP.

### Etapa 8 — Responsividade, acessibilidade e SEO

**Tarefas:** revisar telas, navegação por teclado, semântica, contrastes, imagens, metadados por página, compartilhamento Open Graph, performance e SEO técnico.

**Aceite:** interface utilizável em mobile/desktop; metadados adequados; principais problemas identificados em testes manuais/Lighthouse corrigidos.

### Etapa 9 — Testes, documentação e publicação

**Tarefas:** testar rotas e links, rever escaping e configuração de produção, atualizar README, preparar servidor e realizar deploy.

**Aceite:** site público funciona em hospedagem PHP com document root seguro, links operacionais, erros 404 corretos e documentação reproduzível.

## 9. Protocolo de trabalho para o Codex

1. **Antes de alterar arquivos:** ler este documento, inspecionar a estrutura real do repositório e identificar a etapa solicitada.
2. **Escopo fechado:** implementar apenas a etapa explicitamente pedida.
3. **Preservar o existente:** não sobrescrever implementações funcionais ou reorganizar a arquitetura sem necessidade comprovada.
4. **Separação MVC:** Models não renderizam, Views não consultam arquivos diretamente, Controllers não concentram grandes blocos HTML.
5. **Mudanças mínimas:** não introduzir bibliotecas, recursos ou camadas extras não exigidos pelo escopo.
6. **Segurança por padrão:** tratar dados de entrada e escape de saída adequadamente.
7. **Testes:** rodar os testes existentes e `php -l` nos arquivos PHP criados/alterados quando possível; reportar o que não foi executado.
8. **Sem suposições biográficas:** não inventar dados pessoais, contatos, projetos ou resultados profissionais.
9. **Ao encerrar a etapa:** apresentar resumo, arquivos criados/modificados, testes realizados, limitações e critérios de aceite pendentes.
10. **Parar após a entrega:** não iniciar a próxima etapa sem novo pedido.

### Prompt-base para iniciar uma sessão no Codex

```text
Leia o arquivo PROJECT_CONTEXT.md e examine a estrutura atual do repositório.
Trabalhe exclusivamente na etapa [NÚMERO] — [NOME] descrita no roadmap.
Respeite a arquitetura MVC, mantenha o código existente e implemente apenas
as tarefas necessárias para satisfazer os critérios de aceite dessa etapa.
Não adicione funcionalidades futuras, frameworks PHP completos ou dependências
sem justificativa. Ao concluir, resuma os arquivos alterados, testes realizados,
resultados, limitações e eventuais pendências. Não avance para a etapa seguinte.
```

## 10. Controle de progresso

- [x] Etapa 1 — Estrutura MVC e roteamento
- [x] Etapa 2 — Layout e identidade visual
- [x] Etapa 3 — Página inicial e sobre mim
- [x] Etapa 4 — Catálogo de projetos
- [x] Etapa 5 — Detalhes dos projetos
- [x] Etapa 6 — Habilidades técnicas
- [x] Etapa 7 — Contato e redes profissionais
- [x] Etapa 8 — Responsividade, acessibilidade e SEO
- [ ] Etapa 9 — Testes, documentação e deploy

Atualize o checklist quando uma etapa for validada. A conclusão de uma implementação não significa que os critérios de aceite foram automaticamente satisfeitos.

Etapa 2 validada em 18/10/2026: home e 404 utilizam o mesmo layout, links e CSS conferidos via HTTP no Apache e no servidor PHP. A base de responsividade está implementada; inspeção visual e testes reais de teclado permanecem pendentes por indisponibilidade de navegador nesta sessão. Detalhes e evidências em `PROGRESS.md`.

Etapa 3 validada funcionalmente em 18/10/2026: home com hero, sobre mim, objetivos e chamada para uma seção informativa de projetos. Dados pessoais ausentes estão explicitamente sinalizados; nenhuma informação biográfica foi inventada. Navegação e âncoras conferidas na home e a partir do 404, inclusive em subdiretórios. O catálogo e a rota `/projetos` permanecem para a etapa 4. Inspeção visual e interação real por teclado continuam pendentes por indisponibilidade de navegador; evidências em `PROGRESS.md`.

Etapa 4 validada funcionalmente em 18/10/2026: catálogo GET `/projetos`, dados em `config/projects.php`, Model com `all()`/`featured()`, cards reutilizados e destaques na home. O catálogo real inicia vazio por falta de projetos confirmados; fixtures isoladas demonstram inclusão por arquivo, campos opcionais, escaping e filtragem de URLs. Home/catálogo retornam 200, detalhes permanecem 404 para a etapa 5. Validação visual e teclado real continuam pendentes por indisponibilidade de navegador; evidências em `PROGRESS.md`.

Etapa 5 validada funcionalmente em 18/10/2026: rota GET `/projetos/{slug}`, consulta validada por slug, página individual com descrição em texto escapado, opcionais, links nos títulos dos cards e retorno ao catálogo. Fixtures isoladas validaram detalhes 200 e slugs inválidos/desconhecidos 404; a integração com o front controller/autoload foi verificada em cópia temporária sem alterar o catálogo real vazio. Inspeção visual e teclado real continuam pendentes por indisponibilidade de navegador. Detalhes em `PROGRESS.md`.

Etapa 6 validada funcionalmente em 18/10/2026: seção reutilizável `/#habilidades`, dados por categoria em `config/skills.php`, Model `Skill` e integração pelo `HomeController`. O arquivo real permanece vazio por falta de conhecimentos confirmados; a home exibe aviso explícito. Fixtures isoladas e HTTP demonstram categorias/listas, atualização só pelos dados, estado vazio, escaping e navegação com caminho-base. Suíte com 696 verificações aprovada. Inspeção visual e teclado real continuam pendentes por indisponibilidade de navegador. Evidências em `PROGRESS.md`.

Etapa 7 implementada e validada funcionalmente com fixtures em 18/10/2026: seção `/#contato`, configuração `config/contact.php`, Model `Contact`, links individuais de GitHub/LinkedIn, `mailto:` e download de PDF local opcional. O arquivo real permanece com valores nulos e aviso explícito, pois não há contatos ou currículo confirmados. Suíte com 782 verificações e integração HTTP aprovadas, incluindo PDF de teste servido com status 200 e MIME correto. Disponibilidade dos perfis reais, destinatário no aplicativo de e-mail e currículo final permanecem pendentes dos dados do proprietário; detalhes em `PROGRESS.md`.

Etapa 8 implementada com validação parcial em 18/10/2026: ajustes de navegação/grid para telas estreitas e textos longos, áreas dos links dos cards, semântica de listas, dimensões reais das imagens, metadados específicos por página, noindex no 404 e Open Graph com imagem padrão. `APP_ORIGIN` habilita canonical e URLs absolutas quando houver domínio confirmado; não é inferida da requisição. Suíte com 967 verificações, sintaxe e integração HTTP aprovadas; contrastes dos tokens verificados. Checkbox permanece pendente: revisão visual mobile/desktop, zoom, teclado real, console e Lighthouse não puderam ser executados pela indisponibilidade de navegador da ferramenta/Lighthouse. Domínio, robots/sitemap e preview público continuam pendentes. Não iniciar a etapa 9 sem novo pedido; detalhes em `PROGRESS.md`.

Retomada da etapa 8 em 18/10/2026: Lighthouse instalado apenas em `/tmp`, com auditorias mobile/desktop e inspeção estática das capturas em 320, 375, 768 e 1280 CSS px. Adicionado favicon local no layout compartilhado para eliminar o erro de console encontrado; respeita caminho-base e identidade visual. Home, catálogo e detalhes de fixtures obtiveram 100 nas quatro categorias avaliadas. Suíte ampliada para 979 verificações aprovadas. O aceite continua parcial porque zoom real e navegação manual por teclado não puderam ser executados; a ferramenta de interação permanece sem navegador conectado. Dados reais preservados; etapa 9 não iniciada. A seção 0 de `PROGRESS.md` contém as evidências atuais e substitui as limitações de ferramentas registradas na implementação inicial acima.

Aceite da etapa 8 concluído em 18/10/2026: o proprietário confirmou que realizou os testes e que a responsividade e a acessibilidade estão corretas. Essa confirmação encerra a pendência manual registrada acima e complementa as auditorias Lighthouse e os testes automatizados anteriores; não houve nova execução manual pelo agente. A próxima etapa pendente é a etapa 9, ainda não iniciada. Domínio, hospedagem, conteúdo pessoal e SEO dependente da publicação continuam pendentes; detalhes na seção 0 de `PROGRESS.md`.

## 11. Decisões e informações pendentes

Situação após o povoamento autorizado em 19/10/2026:

- [x] Título profissional: Desenvolvedor Web Full Stack.
- [x] Nome exibido: Heitor Silva Vareiro.
- [x] Texto “Sobre mim”, formação em andamento, estágio, certificações e objetivos.
- [x] URLs reais de GitHub e LinkedIn.
- [x] Divulgação de `heitorsvareiro@gmail.com` autorizada e link disponível em Contato (21/10/2026).
- [x] Currículo `CurriculoLinkedin.pdf` fornecido e disponibilizado para download (21/10/2026).
- [x] Lista de projetos, textos, tecnologias e repositórios ativos.
- [ ] Capturas reais dos projetos.
- [x] Habilidades confirmadas pelo currículo e guia.
- [x] Referência de identidade visual: portfólio iuricode (repositório e site na seção 1).
- [ ] Detalhes da identidade visual (claro/escuro, paleta, tipografia) e imagens de destaque.
- [ ] Domínio, provedor de hospedagem e configuração do servidor web.

Na ausência dessas definições, utilizar escolhas técnicas reversíveis e placeholders sinalizados; não bloquear a criação da estrutura inicial.

Povoamento concluído sem publicação: cinco projetos cadastrados e quatro visíveis. `visible => false` exclui o Portfólio MVC em PHP do catálogo, destaques e consulta por slug até a confirmação do deploy. Por decisão do proprietário, Gerenciador de Permanências e Crowns aparecem em destaque, sem revisão de segurança como condição editorial. Todos os projetos têm status “Finalizado · fora de produção”; as descrições preservam as limitações verificadas, incluindo persistência parcial do Crowns. Festividades de Fim de Ano e Notícias são secundários; somente Festividades possui demo confirmada. `status` opcional informa ressalvas nos cards e detalhes. Imagens ausentes são omitidas. Arquitetura MVC, rotas e dependências preservadas; etapa 9 continua pendente de publicação. Testes atuais e limitações estão na seção 0 de `PROGRESS.md`; registros de etapas anteriores acima são históricos.

Base visual adotada na etapa 2, ainda sujeita à validação do proprietário: tema escuro, destaque violeta, tipografia do sistema e espaçamentos definidos por variáveis CSS. Imagens de destaque não foram adicionadas nesta etapa.

## 12. Evoluções após o MVP

Funcionalidades opcionais, somente após publicação da versão inicial: painel administrativo com autenticação segura, persistência em MySQL, CRUD de projetos, filtros por tecnologia, modo claro/escuro, versão em inglês e automação de deploy. Cada evolução deve ser planejada separadamente, com requisitos, migrações e testes específicos.

---

**Fonte de verdade:** este documento orienta o desenvolvimento; o estado atual do repositório e as decisões explícitas do proprietário prevalecem quando surgirem divergências. Revisar o documento conforme as decisões forem consolidadas.
