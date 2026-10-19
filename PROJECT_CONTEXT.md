# Contexto do Projeto — Portfólio Pessoal PHP MVC

> Documento de referência para desenvolvimento incremental com Codex. Deve ser consultado antes de implementar qualquer etapa.

## 1. Visão geral

Desenvolver um **site de portfólio pessoal** para apresentar o desenvolvedor, seus projetos, habilidades e meios de contato. O endereço será divulgado no **LinkedIn, GitHub e currículo**. A experiência deve ser profissional, acessível, responsiva, rápida e simples de manter.

O site será o portfólio pessoal do proprietário, com posicionamento profissional de **Desenvolvedor Full Stack**. Os detalhes dos projetos realizados e das habilidades técnicas serão fornecidos futuramente pelo proprietário; até lá, não atribuir projetos ou competências sem confirmação.

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

**Importante:** nome completo de exibição, biografia final, URLs de perfis, endereço de e-mail e currículo ainda precisam ser fornecidos pelo proprietário. A referência de identidade visual está registrada na seção 1; seus detalhes serão definidos na etapa de layout. Os dados reais de projetos e habilidades serão informados futuramente. Usar placeholders evidentes até receber dados reais. **Nunca inventar experiências, formação, competências ou resultados de projetos.**

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
- [ ] Etapa 8 — Responsividade, acessibilidade e SEO
- [ ] Etapa 9 — Testes, documentação e deploy

Atualize o checklist quando uma etapa for validada. A conclusão de uma implementação não significa que os critérios de aceite foram automaticamente satisfeitos.

Etapa 2 validada em 18/10/2026: home e 404 utilizam o mesmo layout, links e CSS conferidos via HTTP no Apache e no servidor PHP. A base de responsividade está implementada; inspeção visual e testes reais de teclado permanecem pendentes por indisponibilidade de navegador nesta sessão. Detalhes e evidências em `PROGRESS.md`.

Etapa 3 validada funcionalmente em 18/10/2026: home com hero, sobre mim, objetivos e chamada para uma seção informativa de projetos. Dados pessoais ausentes estão explicitamente sinalizados; nenhuma informação biográfica foi inventada. Navegação e âncoras conferidas na home e a partir do 404, inclusive em subdiretórios. O catálogo e a rota `/projetos` permanecem para a etapa 4. Inspeção visual e interação real por teclado continuam pendentes por indisponibilidade de navegador; evidências em `PROGRESS.md`.

Etapa 4 validada funcionalmente em 18/10/2026: catálogo GET `/projetos`, dados em `config/projects.php`, Model com `all()`/`featured()`, cards reutilizados e destaques na home. O catálogo real inicia vazio por falta de projetos confirmados; fixtures isoladas demonstram inclusão por arquivo, campos opcionais, escaping e filtragem de URLs. Home/catálogo retornam 200, detalhes permanecem 404 para a etapa 5. Validação visual e teclado real continuam pendentes por indisponibilidade de navegador; evidências em `PROGRESS.md`.

Etapa 5 validada funcionalmente em 18/10/2026: rota GET `/projetos/{slug}`, consulta validada por slug, página individual com descrição em texto escapado, opcionais, links nos títulos dos cards e retorno ao catálogo. Fixtures isoladas validaram detalhes 200 e slugs inválidos/desconhecidos 404; a integração com o front controller/autoload foi verificada em cópia temporária sem alterar o catálogo real vazio. Inspeção visual e teclado real continuam pendentes por indisponibilidade de navegador. Detalhes em `PROGRESS.md`.

Etapa 6 validada funcionalmente em 18/10/2026: seção reutilizável `/#habilidades`, dados por categoria em `config/skills.php`, Model `Skill` e integração pelo `HomeController`. O arquivo real permanece vazio por falta de conhecimentos confirmados; a home exibe aviso explícito. Fixtures isoladas e HTTP demonstram categorias/listas, atualização só pelos dados, estado vazio, escaping e navegação com caminho-base. Suíte com 696 verificações aprovada. Inspeção visual e teclado real continuam pendentes por indisponibilidade de navegador. Evidências em `PROGRESS.md`.

Etapa 7 implementada e validada funcionalmente com fixtures em 18/10/2026: seção `/#contato`, configuração `config/contact.php`, Model `Contact`, links individuais de GitHub/LinkedIn, `mailto:` e download de PDF local opcional. O arquivo real permanece com valores nulos e aviso explícito, pois não há contatos ou currículo confirmados. Suíte com 782 verificações e integração HTTP aprovadas, incluindo PDF de teste servido com status 200 e MIME correto. Disponibilidade dos perfis reais, destinatário no aplicativo de e-mail e currículo final permanecem pendentes dos dados do proprietário; inspeção visual e teclado real indisponíveis nesta sessão. Etapa 8 não iniciada; detalhes em `PROGRESS.md`.

## 11. Decisões e informações pendentes

Antes de substituir placeholders e publicar, confirmar:

- [x] Título profissional: Desenvolvedor Full Stack.
- [ ] Nome exibido no site.
- [ ] Texto “Sobre mim”, formação e objetivos.
- [ ] URLs reais de GitHub e LinkedIn, e-mail profissional e currículo (se houver).
- [ ] Lista real de projetos, textos, imagens, tecnologias e repositórios.
- [ ] Quais habilidades serão listadas.
- [x] Referência de identidade visual: portfólio iuricode (repositório e site na seção 1).
- [ ] Detalhes da identidade visual (claro/escuro, paleta, tipografia) e imagens de destaque.
- [ ] Domínio, provedor de hospedagem e configuração do servidor web.

Na ausência dessas definições, utilizar escolhas técnicas reversíveis e placeholders sinalizados; não bloquear a criação da estrutura inicial.

Base visual adotada na etapa 2, ainda sujeita à validação do proprietário: tema escuro, destaque violeta, tipografia do sistema e espaçamentos definidos por variáveis CSS. Imagens de destaque não foram adicionadas nesta etapa.

## 12. Evoluções após o MVP

Funcionalidades opcionais, somente após publicação da versão inicial: painel administrativo com autenticação segura, persistência em MySQL, CRUD de projetos, filtros por tecnologia, modo claro/escuro, versão em inglês e automação de deploy. Cada evolução deve ser planejada separadamente, com requisitos, migrações e testes específicos.

---

**Fonte de verdade:** este documento orienta o desenvolvimento; o estado atual do repositório e as decisões explícitas do proprietário prevalecem quando surgirem divergências. Revisar o documento conforme as decisões forem consolidadas.
