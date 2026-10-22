# Portfólio MVC em PHP

Site de portfólio pessoal em PHP, com arquitetura MVC própria, catálogo e páginas individuais de projetos. A aplicação renderiza HTML no servidor e utiliza arquivos PHP de configuração como fonte dos dados, sem framework ou banco de dados.

Portfólio de Heitor Silva Vareiro, Desenvolvedor Web Full Stack e estudante de Técnico Integrado em Informática no IFMS. Conteúdo profissional preenchido; site publicado em https://heitorvareiro.heitorsvareiro.workers.dev/.

O avatar circular do início utiliza o retrato fornecido e autorizado pelo responsável, `public/assets/images/meu-retrato.png`, também usado como favicon PNG. O avatar recebe zoom de 15% por CSS, preservando o arquivo original. Conferido no Chrome em 390/1440px nos dois temas; [capturas com o retrato e zoom](design/validation/portrait-favicon). As capturas e medições da etapa 9 abaixo são anteriores à inclusão da imagem.

Estado atual: o contato autorizado `heitorsvareiro@gmail.com` e o currículo `public/assets/documents/CurriculoLinkedin.pdf` estão configurados na seção de contato. Os perfis do GitHub e LinkedIn, o e-mail e o download do currículo usam links no mesmo estilo, sem sublinhado. O redesenho está registrado no commit `b20aacf` (`style: layout do site reformulado`) e enviado para `main` no GitHub. O site está publicado no Cloudflare Workers com Static Assets. As seções de progresso abaixo registram o contexto histórico de cada etapa.

## Publicação automática no Cloudflare Workers

Produção: **https://heitorvareiro.heitorsvareiro.workers.dev/**. O nome configurado do Worker é **`heitorvareiro`**; `heitorsvareiro` é o subdomínio da conta. Esse nome já constava no `wrangler.jsonc` local e foi confirmado pelo responsável, que também informou a configuração dos secrets no GitHub. O CI verifica o Worker e o subdomínio pela API antes da publicação. Não crie outra aplicação.

O [workflow](.github/workflows/deploy.yml) publica somente em `Heitorsv001/Portifolio`, na branch `main`, por `push` ou **Run workflow** (`workflow_dispatch`). PHP MVC, configurações e assets continuam sendo a fonte; `dist/` é gerada e permanece ignorada pelo Git. O [wrangler.jsonc](wrangler.jsonc) aponta exclusivamente para `./dist`, preserva `workers_dev: true` e não contém credenciais, script PHP ou configuração de Pages.

A entrega usa `force-trailing-slash` para páginas HTML e `404-page` para servir `dist/404.html` com status 404 em caminhos inexistentes. Essas opções seguem a [documentação de Static Assets](https://developers.cloudflare.com/workers/static-assets/routing/static-site-generation/).

### Secrets e primeiro deploy

1. Confira no painel o Worker **`heitorvareiro`**, o endereço acima e o **Account ID** da conta proprietária. Antes da primeira publicação, registre a versão/deployment atualmente saudável para eventual rollback.
2. Crie um token da Cloudflare com **Account → Workers Scripts → Edit**, limitado à conta proprietária. Essa permissão permite publicar assets e consultar o Worker/subdomínio; não é necessário conceder permissões de Pages ou DNS para este endereço. Não use Global API Key.
3. No repositório, abra **Settings → Secrets and variables → Actions → New repository secret** e configure:

   | Secret | Valor |
   | --- | --- |
   | `CLOUDFLARE_API_TOKEN` | Token com permissão de Workers Scripts na conta correta |
   | `CLOUDFLARE_ACCOUNT_ID` | ID de 32 caracteres da conta Cloudflare proprietária do Worker |

4. Revise os arquivos antes de autorizar seu commit/push. O workflow só existe localmente nesta entrega; nenhuma execução remota foi iniciada. Para fazer a estreia manualmente, mantenha **Actions desabilitado durante o primeiro push** do workflow para `main`; depois habilite-o e abra **Actions → Deploy do portfólio no Workers → Run workflow → main**. Se Actions e os secrets já estiverem ativos nesse primeiro push, ele iniciará automaticamente o deploy.
5. Acompanhe todas as etapas. Depois de uma execução concluída, confira home, `/projetos/`, todos os detalhes, PDF, temas e menu; `/projetos` deve redirecionar para `/projetos/` e uma URL inexistente deve retornar a página personalizada com HTTP 404. Confira também canonical, `robots.txt` e `sitemap.xml` no endereço público.

Não há etapa de criação de Worker. Antes da publicação, `node scripts/verify-worker.mjs` faz apenas consultas GET à [API do Worker existente](https://developers.cloudflare.com/api/resources/workers/subresources/scripts/subresources/subdomain/methods/get/) e ao [subdomínio da conta](https://developers.cloudflare.com/api/resources/workers/subresources/subdomains/methods/get/). Worker ausente, conta/endereço divergente, `workers.dev` desabilitado, segredo ausente ou erro da API interrompem o job. Não copie os secrets para código, `.env`, saída estática ou logs.

### Atualizações e verificações do CI

Após a ativação, cada push na `main` executa, em sequência:

1. PHP **8.4**, Composer **2**, extensão **DOM** e Node **24**; instalação pelo `composer.lock` e validação do autoload.
2. Sintaxe PHP, `tests/run.php`, `tests/static-export.php`, os testes JavaScript de tema/navegação/partículas e `node --test tests/verify-worker.mjs`.
3. `php scripts/export-static.php --target=cloudflare` com `APP_BASE_PATH=''` e `APP_ORIGIN='https://heitorvareiro.heitorsvareiro.workers.dev'`.
4. Testes HTTP/visuais em Chromium com Playwright **1.64.1**, instalado apenas na pasta temporária do runner, e `php scripts/validate-static.php --target=cloudflare` sobre a saída final. O validador confere rotas, links, SEO, hashes dos assets e inventário permitido; recusa PHP, arquivos internos, extras e links simbólicos.
5. Armazenamento do artefato validado `portfolio-static-<SHA>` por **30 dias**, verificação do destino existente e publicação com `cloudflare/wrangler-action` v3 fixada por SHA e Wrangler **4.152.0**, via `deploy --config wrangler.jsonc`.

Todas as etapas exigem sucesso das anteriores. Credenciais da Cloudflare são disponibilizadas apenas na checagem remota e na publicação. O token do GitHub usa somente `contents: read`. A concorrência usa um grupo único de produção, com `cancel-in-progress: false`: uma publicação em andamento termina antes de outra execução começar. O GitHub pode substituir uma execução ainda pendente por outra mais recente; execuções em andamento não são canceladas. Evite outro mecanismo de deploy automático do mesmo Worker, inclusive integração de build da Cloudflare, para não haver disputa entre publicadores.

Para atualizar o site, altere o PHP/configuração/assets, execute os testes locais descritos abaixo e, após revisão, envie a alteração à `main`. Não edite HTML em `dist/`. Para repetir o build da versão atual sem mudança de código, use **Run workflow**, sempre em `main`. O workflow recusa execução manual em outras branches.

### Rollback

1. Desabilite temporariamente este workflow em **Actions → Deploy do portfólio no Workers → Disable workflow** e verifique se há execução em andamento ou pendente. Aguarde a publicação em andamento terminar antes de reverter a produção.
2. No painel **Workers & Pages → heitorvareiro → Deployments**, selecione uma versão saudável anterior e use **Rollback**. Confira o Worker e a versão antes de confirmar. O procedimento restaura uma versão do mesmo Worker e preserva seu endereço; veja [rollbacks de Workers](https://developers.cloudflare.com/workers/versions-and-deployments/rollbacks/).
3. Valide novamente rotas, 404, redirects, assets e conteúdo público. Uma versão antiga pode voltar a expor conteúdo que foi ocultado depois.
4. Corrija ou reverta também a alteração de origem na `main` em uma mudança revisada (por exemplo, `git revert <SHA_DO_COMMIT_COM_PROBLEMA>`). O rollback da Cloudflare não altera o Git: habilitar o workflow e publicar a mesma revisão defeituosa traria o problema de volta.
5. Reabilite o workflow depois da correção e execute manualmente em `main`. Se a versão desejada não estiver mais disponível na Cloudflare, restaure o conteúdo de uma revisão saudável no código, teste e publique pela mesma sequência de CI. Os artefatos retidos no Actions servem para inspeção/comparação; este workflow sempre reconstrói da fonte e não possui entrada para publicar ZIP arbitrário.

Validação desta automação em 22/10/2026: **1.601/1.601 PHP**, **135/135 exportação**, **139/139 JavaScript**, **4/4 testes da proteção do destino**, **172/172 HTTP/navegador**, **32 comparações visuais idênticas** e **36 arquivos PHP sem erros de sintaxe**. Build e validação final com a origem pública aprovados: seis páginas, 404, sete assets e 16 arquivos permitidos. Composer 2 validou o manifesto e gerou autoload otimizado com PSR-4 estrito; o único aviso do manifesto é a licença não declarada, já existente. `actionlint` 1.7.12 aprovou o workflow (sem ShellCheck); `git diff --check` aprovado. Os testes MVC foram executados em cópia temporária; os de exportação isolam suas próprias fixtures. A suíte HTTP usou Chrome local e Playwright disponível no runtime; a instalação de Chromium da versão fixada no CI ainda depende da primeira execução no GitHub. Não foi executado `wrangler deploy`, nem mesmo em dry-run, e não houve chamada autenticada à Cloudflare.

O responsável confirmou o nome do Worker, informou a configuração dos dois secrets e autorizou commit e push para `main`. A primeira validação da entrega real deve ser acompanhada no GitHub Actions; o push inicia o workflow quando Actions está habilitado. [STATIC_DEPLOY_PLAN.md](STATIC_DEPLOY_PLAN.md) e [STATIC_EXPORT_VALIDATION.md](STATIC_EXPORT_VALIDATION.md) preservam o planejamento e as evidências anteriores; suas referências a Pages são históricas. A configuração atual é exclusivamente Workers.

## Exportação estática — validação local em 22/10/2026

As etapas 1–5 de [STATIC_DEPLOY_PLAN.md](STATIC_DEPLOY_PLAN.md) estão implementadas. O PHP MVC continua sendo a fonte de desenvolvimento; o exportador executa a entrada real `public/index.php` em processos PHP isolados e grava HTML. O destino atual é Cloudflare Workers com Static Assets. O perfil `--target=cloudflare` gera o artefato estático; ele não configura Cloudflare Pages nem executa deploy.

Requisitos para geração: **PHP CLI 8.2+ com DOM (`ext-dom`), `proc_open()` habilitado e autoload Composer disponível**. A aplicação web original continua sem dependência de DOM. Em um checkout novo, execute `composer install`; não há pacotes externos no lock atual. Não há framework ou build JavaScript novo. Node e Playwright são ferramentas opcionais para os testes de navegador, não requisitos do site publicado.

Execute na raiz com o endereço público atual:

```sh
APP_BASE_PATH='' APP_ORIGIN='https://heitorvareiro.heitorsvareiro.workers.dev' php scripts/export-static.php --target=cloudflare
APP_BASE_PATH='' APP_ORIGIN='https://heitorvareiro.heitorsvareiro.workers.dev' php scripts/validate-static.php --target=cloudflare
```

`APP_ORIGIN` aceita apenas origem HTTPS, sem caminho, credenciais, query ou fragmento. `APP_BASE_PATH` precisa ser explícito: vazio ou `/` na raiz; `/Portifolio` para um site de projeto com esse nome confirmado. O gerador ativa `APP_STATIC_EXPORT` internamente e não exige alterações na configuração local. Essa opção não altera o funcionamento do PHP web.

Para testar também a saída para GitHub Pages:

```sh
APP_BASE_PATH='/Portifolio' APP_ORIGIN='https://heitorsv001.github.io' php scripts/export-static.php --target=github
APP_BASE_PATH='/Portifolio' APP_ORIGIN='https://heitorsv001.github.io' php scripts/validate-static.php --target=github
```

Cada geração substitui a saída completa. Não colocar arquivos manuais em `dist/`: páginas removidas/ocultadas e assets sem referência desaparecem na reconstrução. Alterações de conteúdo, view, asset, origem ou prefixo exigem novo build. Um erro retorna código diferente de zero, elimina a área temporária e preserva `dist/` anterior; não enviar essa saída antiga como se o build tivesse sido concluído. O lock impede exportações simultâneas.

O artefato atual tem **16 arquivos**: seis páginas públicas, `404.html`, `sitemap.xml`, `robots.txt` e sete assets necessários. O PDF, os dois PNGs utilizados, CSS e três scripts mantêm seus bytes. SVGs não utilizados, `.gitkeep`, PHP, configurações, dependências, documentação e testes ficam fora da saída. Referências em HTML e dependências relativas de CSS determinam a cópia; futuras imagens/fontes referenciadas também serão incluídas. Recursos carregados dinamicamente por um futuro JavaScript exigirão revisão do coletor, pois ele não executa scripts para descobrir arquivos.

As páginas estáticas usam `/projetos/` e `/projetos/{slug}/`. Os slugs permanecem iguais; o PHP habitual conserva suas URLs sem barra final. O projeto `portfolio-mvc-php` permanece oculto. O sitemap usa apenas páginas públicas, sem data de atualização inventada; canonical, `og:url`, navegação e sitemap incluem origem/prefixo corretos. O perfil GitHub acrescenta `.nojekyll`, sem criar `dist/Portifolio/`. Novas rotas fixas precisam ser incluídas em `pages()` de `scripts/static-support.php`; novos projetos visíveis entram automaticamente pelo Model.

Para um preview HTTP simples na raiz, com Python disponível:

```sh
python3 -m http.server 8080 --bind 127.0.0.1 --directory dist
```

Abra `http://127.0.0.1:8080/`; não use `file://`. Esse servidor simples exibe sua própria página para URLs inexistentes. O teste de navegador abaixo fornece um servidor estático temporário com a `404.html` e o status 404, sem executar PHP no artefato. Para prefixos, use esse teste ou monte a árvore sob o caminho correspondente. O comportamento real de normalização e 404 no provedor ainda precisa de validação após publicação autorizada. No GitHub Pages de projeto, `robots.txt` dentro do prefixo não controla a raiz do domínio.

Testes da exportação e interações existentes:

```sh
php tests/static-export.php
node tests/theme.js
node tests/navigation.js
node tests/particles.js
```

`tests/static-export.php` cria uma cópia isolada em `/tmp`, testa builds/erros e remove suas fixtures. `tests/run.php` mantém a suíte MVC existente e cria arquivos temporários em `public/assets/`; executá-la em uma cópia temporária evita concorrência com o build real.

Com Playwright disponível na resolução de módulos Node e Chrome/Chromium instalado:

```sh
CHROME_BIN=/caminho/para/chrome APP_BASE_PATH='' APP_ORIGIN='https://portfolio.example' node tests/static-browser.js
```

Se o módulo estiver instalado fora do projeto, defina `NODE_PATH` para essa pasta de ferramentas. Se usar o navegador instalado pelo Playwright, omita `CHROME_BIN`. O teste inicia e encerra seus próprios servidores restritos a localhost e seu navegador, compara PHP/estático sem animação e testa partículas separadamente. `STATIC_SCREENSHOT_DIR` permite guardar quatro capturas de home fora de `dist/`. Não usa contas ou faz deploy.

Resultados locais: **1.601/1.601 PHP**, **135/135 exportação**, **139/139 JavaScript**, **172/172 HTTP/navegador**, **32 comparações visuais idênticas** e **36 arquivos PHP sem erros de sintaxe**. O teste antigo de currículo isolado foi alinhado à lista de contatos aprovada no redesign, verificando um único download na lista; a apresentação não foi alterada. Evidências e limitações em [STATIC_EXPORT_VALIDATION.md](STATIC_EXPORT_VALIDATION.md).

O artefato reconstruído nesta tarefa usa `https://heitorvareiro.heitorsvareiro.workers.dev`. Reconstrua após qualquer alteração de conteúdo. O gerador não acessa a hospedagem; somente a etapa final do workflow abaixo publica.

## Redesign — etapa 9: revisão e validação local

Executada em 21/10/2026, após o aceite da etapa 8 e do contato por e-mail. Corrigida a atualização das partículas ao mudar preferências de movimento durante a visita: a permissão agora é atualizada por eventos, fora do ciclo de desenho. Acrescentado teste de regressão para evitar consultas de mídia a cada quadro. Nenhum texto, dado profissional, Model, rota ou dependência alterado nesta etapa.

Validação atual: **1.601/1.601 verificações PHP**, **139/139 JavaScript** em V8 com simulação, sintaxe dos **30 arquivos PHP** e `git diff --check` aprovados. Chrome instalado, com perfil temporário e DevTools: **480/480 verificações** em 48 combinações de página/largura/tema, **39/39 de interação** e **6/6 de partículas**, mais oito casos com raiz pública. Conferidos teclado/foco, temas, armazenamento indisponível, JavaScript desativado, texto ampliado a 200%, contraste calculado, console, rede e movimento. Não encontrado overflow nas larguras de 320 a 1440px avaliadas. Rotas e assets aprovados em `/portifolio` e com `public/` como raiz.

[Relatório e capturas](design/REDESIGN_VALIDATION.md): 30 PNGs do próprio portfólio e evidências JSON, incluindo medições locais de performance. Perfis de navegador e servidor PHP auxiliar usados apenas para a revisão e encerrados ao término. As amostras de performance não são resultados Lighthouse nem estimativas de hospedagem. LinkedIn bloqueou a consulta automatizada com status 999; demais links públicos consultados responderam 200. Aceite visual final, Lighthouse, zoom nativo e avaliação em dispositivos/tecnologias assistivas continuam pendentes. Sem publicação, commit ou push. Os registros das etapas abaixo são históricos.

## Redesign — etapa 8: contato e rodapé

Implementada em 21/10/2026, após o aceite da etapa 7 e dos ajustes de redundância de Sobre mim. Rodapé discreto com nome, título profissional, ícones nomeados de GitHub/LinkedIn e retorno ao topo em home, catálogo, detalhes e páginas 404. Reutiliza o mesmo `social-links.php` do hero. O Controller base fornece contatos normalizados pelo Model quando a página não os informa; contatos injetados na home continuam compartilhados entre hero, seção de contato e rodapé.

Contato mantém os links escritos e recebe grid fluido, bordas e áreas de clique de pelo menos 44px na fonte padrão. E-mail e currículo continuam condicionais e não foram publicados. Os ícones do rodapé têm área de clique de 44px, SVG de 22px, foco compartilhado e cores dos temas. Listas de redes ausentes são omitidas; a identificação e o retorno ao topo permanecem. Nenhum formulário, dado profissional, rota, Model ou dependência alterado nesta etapa.

Validação: **1.602/1.602 verificações PHP** com `E_ALL` em cópia temporária; **138/138 JavaScript** de regressão em V8 com simulação; sintaxe dos **30 arquivos PHP** e `git diff --check` aprovados. Apache: home, catálogo e quatro detalhes **200**; projeto oculto, slug desconhecido e rota inexistente **404**, todos com as duas redes nomeadas no rodapé, IDs únicos e sem e-mail/PDF ou avisos PHP. CSS versionado **200**, MIME e conteúdo corretos. Testes cobrem escaping, contatos completos/parciais/ausentes e preservação de `mailto:`/PDF opcionais com fixtures. Revisão visual nos dois temas, responsividade e teclado reais pendentes por falta de navegador conectado. Etapa 9 não iniciada; sem commit ou push.

## Redesign — etapa 7: habilidades, formação e certificações

Implementada em 21/10/2026, após o aceite da etapa 6 pelo responsável. As oito categorias e os 29 itens de habilidades foram preservados. Cada nome recebe um SVG decorativo com símbolo da categoria, renderizado localmente, sem pacote ou requisição externa. Os nomes permanecem legíveis sem os ícones; categorias futuras recebem um símbolo genérico.

Formação e certificações agora ocupam a seção `#formacao`, após habilidades e antes de contato. Curso, IFMS, período com previsão, situação “Em andamento” e os quatro nomes de certificações continuam vindo do perfil existente, sem emissores, datas ou níveis adicionais. Contato teve apenas sua numeração ajustada à sequência das seções. PHP MVC, dados, rotas e dependências preservados.

Validação: **1.362/1.362 verificações PHP** com `E_ALL` em cópia temporária, **138/138 JavaScript** de regressão em V8 com simulação, sintaxe dos **30 arquivos PHP** e `git diff --check` aprovados. Apache confirmou home, catálogo e quatro detalhes **200**, projeto oculto e rota inexistente **404**, dados/ordem das seções, ícones com nomes, IDs únicos e CSS versionado com MIME/conteúdo corretos. Revisão visual desta etapa nos dois temas, responsividade e teclado real pendentes por falta de navegador conectado. Etapa 8 (contato e rodapé) não iniciada; sem commit ou push.

## Redesign — etapa 6: projetos e detalhes

Implementada em 21/10/2026, após o aceite da etapa 5 pelo responsável. A home apresenta Gerenciador de Permanências e Crowns em destaques amplos, seguidos de Festividades de Fim de Ano e Notícias em lista compacta. O HomeController separa os grupos a partir de uma única consulta aos projetos visíveis, preservando a ordem e as flags existentes. Nenhum projeto aparece nos dois grupos.

O partial de listagem atende três apresentações (destaque, compacta e catálogo), compartilhando títulos, status, resumos, tecnologias e links. Capturas reais, quando cadastradas, ficam ao lado do texto nos destaques em telas largas e acima no mobile; sem imagem, o texto usa toda a largura. Nenhuma captura foi inventada. A lista compacta privilegia texto e links; imagens cadastradas continuam disponíveis no catálogo e detalhes.

O catálogo `/projetos` mantém quatro itens e acesso por URL direta. Detalhes preservam descrição integral, limitações, status, tecnologias e links; “Voltar aos projetos” retorna a `/#projetos`, e “Catálogo completo” mantém acesso à listagem. Portfólio MVC em PHP continua invisível e retorna 404. Os dados em `config/projects.php` e as regras do Model não foram alterados.

Validação: **1.326/1.326 verificações PHP** em cópia temporária, **138/138 JavaScript** de regressão em V8 com simulação, sintaxe dos **30 arquivos PHP**, `git diff --check` e integração Apache aprovados. Confirmados grupos, ausência de duplicação, imagens/opcionais, escaping, retornos e projeto oculto. Demos de Festividades e Notícias responderam **HTTP 200** nesta execução. Revisão visual dos dois temas, responsividade e teclado real continuam pendentes por falta de navegador conectado. Etapa 7 (habilidades e formação) não iniciada.

## Redesign — etapa 5: experiência e Sobre

Implementada em 21/10/2026, após o aceite das partículas pelo responsável. A experiência profissional é a primeira seção após o hero, com período, cargo de estágio, IFMS, Arco Literário, descrição e tags de Node.js, Express, MongoDB, EJS e Bootstrap 5. Essas tecnologias já constavam da descrição e foram organizadas em `experience.technologies` no perfil, sem alteração dos valores profissionais anteriores. No mobile/tablet, período acima do texto; a partir de 1024px, período em coluna lateral.

Sobre mim preserva introdução, condição de estudante, biografia e objetivos, agora integrados ao mesmo bloco. As âncoras `#experiencia`, `#sobre`, `#objetivos`, `#objetivos-titulo` e `#formacao` continuam disponíveis. Formação e certificações permanecem em Sobre até a etapa 7; nenhuma informação de curso/estágio concluído ou resultado novo foi acrescentada.

Validação: **1.297/1.297 verificações PHP** em cópia temporária; **72/72 de tema**, **29/29 de navegação** e **37/37 de partículas** em V8 com simulação; sintaxe dos **30 arquivos PHP**, `git diff --check` e integração Apache aprovados. Conferidos ordem das seções, dados do estágio, tags escapadas, objetivos e âncoras. Comparação do perfil antes/depois confirmou a preservação integral dos valores anteriores. Revisão visual da etapa 5 e teclado real pendentes por falta de navegador conectado. A etapa 6 (projetos) ainda não foi iniciada.

## Redesign — etapa 4: partículas

Implementada em 21/10/2026, após o responsável confirmar o hero. Canvas decorativo atrás do conteúdo, exclusivo da home, com pontos e conexões discretos e reação suave ao mouse. `public/assets/js/hero-particles.js` é carregado com `defer` e versão pelo conteúdo, sem biblioteca adicional.

O efeito funciona a partir de 900px na fonte padrão e com ponteiro preciso; fica desligado em telas menores, dispositivos de ponteiro impreciso e quando há preferência por movimento reduzido. Respeita mudanças dessas preferências durante a visita. A animação pausa quando a aba está oculta ou o hero sai da viewport, sem acumular loops ao retomar. Limites: até 64 pontos, desenho a no máximo 30 quadros por segundo, pixel ratio até 2 e buffer de aproximadamente 6 milhões de pixels. As cores acompanham os tokens do tema. O canvas não recebe foco nem intercepta cliques, seleção ou rolagem; falhas de API deixam o conteúdo visível sem efeito.

Validação: **1.251/1.251 verificações PHP**, **72/72 de tema**, **29/29 de navegação**, **35/35 de partículas**, sintaxe dos **30 arquivos PHP** e integração Apache aprovadas. Os testes JavaScript usam DOM/Canvas/eventos simulados em V8; verificam limites, pausa, retomada, preferências, cores e mouse. Não comprovam desempenho gráfico real. Sem navegador conectado, permanecem pendentes revisão visual, comparação com efeito desligado, fluidez e inspeção de tarefas longas.

Teste opcional em ambiente de desenvolvimento com Node.js:

```sh
node tests/particles.js
```

## Redesign — etapa 3: hero

Implementada em 21/10/2026, após o responsável confirmar a revisão visual da etapa 2. Hero central com avatar circular neutro, nome, subtítulo exato **“Desenvolvedor Web e estudante”** e somente ícones de GitHub/LinkedIn. Foto pessoal não foi fornecida: o círculo contém um símbolo de código decorativo, sem imagem fictícia ou requisição externa. As redes usam os contatos já normalizados pelo Model existente, em partial reutilizável.

No desktop, o hero considera a altura do header para ocupar a viewport; no mobile, usa uma altura mínima menor e cresce com o conteúdo para evitar cortes. O hero tem largura independente das seções e não altera o tamanho das páginas internas. Título Full Stack, condição acadêmica e introdução foram realocados para Sobre mim, preservando os valores atuais, a biografia e os objetivos. A experiência foi reorganizada na etapa 5; formação e certificações continuam para a etapa 7.

Validação: **1.232/1.232 verificações PHP** em cópia temporária, **72/72 de tema**, **29/29 de navegação** em V8 com DOM/eventos simulados, sintaxe dos **30 arquivos PHP**, `git diff --check` e integração Apache aprovados. Conferidos hero sem CTA, duas redes com nomes acessíveis, estado sem contatos, preservação dos textos e layouts internos. O responsável confirmou o hero antes de autorizar a etapa 4; a substituição futura do avatar por foto autorizada permanece pendente. Rodapé com redes continua previsto para a etapa 8.

## Redesign — etapa 2: header e navegação

Implementada em 21/10/2026, após o responsável confirmar o funcionamento do controle de tema da etapa 1. O header mostra links para Experiência, Sobre, Projetos, Habilidades, Formação e Contato; a marca retorna ao início. O catálogo `/projetos` continua disponível pelo link da seção de projetos e por URL direta.

Em telas a partir de 75rem (1200px na fonte padrão), a home usa header transparente no topo e fundo sólido ao rolar. Páginas internas mantêm fundo sólido. Em telas menores, o menu compacto usa botão de 44 × 44px, estado acessível e painel sem bloqueio de rolagem. Fecha ao escolher destino, usar Escape, clicar fora ou sair com o foco; Escape devolve o foco ao botão. Sem JavaScript ou API de breakpoint, os links permanecem visíveis. A altura medida do header define a margem de rolagem das âncoras.

Experiência e formação receberam âncoras nos títulos existentes, sem mover ou reescrever os dados; sua reorganização continua prevista para etapas posteriores. O hero foi alterado separadamente na etapa 3.

Validação desta etapa: **1.181/1.181 verificações PHP** em cópia temporária, **72/72 de tema** e **29/29 de navegação** em V8 com DOM/eventos simulados; sintaxe dos **29 arquivos PHP** e `git diff --check` aprovados. Apache confirmou home, catálogo e quatro detalhes 200, projeto oculto e rota inexistente 404, âncoras válidas e CSS/scripts versionados 200. Navegador da ferramenta continua indisponível: revisão visual dos dois temas, larguras 360–1440px e teclado real permanecem pendentes.

O teste de navegação também pode ser executado com Node.js, opcional para desenvolvimento:

```sh
node tests/navigation.js
```

## Redesign — etapa 1: tokens e temas

Implementada em 21/10/2026 conforme [design/REDESIGN_PLAN.md](design/REDESIGN_PLAN.md) e [design/DESIGN_GUIDE.md](design/DESIGN_GUIDE.md). Paleta escura/clara com destaque roxo, tokens compartilhados, fonte do sistema e monoespaçada para rótulos/tags. Arquitetura MVC, conteúdo e rotas preservados.

O botão de tema está no header de todas as páginas. A escolha salva em `localStorage` (`portfolio-theme`) prevalece sobre a preferência do sistema; sem ambas, o padrão é escuro. Sem escolha manual, mudanças do sistema atualizam a página. Armazenamento bloqueado não impede a alternância durante a visita. Sem JavaScript, o site permanece no tema escuro e o botão fica oculto.

`public/assets/js/theme-init.js` é carregado antes do CSS para aplicar a preferência inicial; `public/assets/js/site.js`, com `defer`, habilita o controle e acompanha mudanças do sistema. São arquivos locais, sem dependências, código inline ou fontes externas.

CSS e scripts usam URLs versionadas pelo conteúdo para evitar versões antigas em cache após alterações. O botão possui tamanho mínimo de 44 × 44px na fonte padrão e ícones de 22px. A alternância muda as cores do site; a meta `theme-color` também sugere uma cor à barra em navegadores compatíveis, sem alterar o tema global do navegador. Após o relato de falha na home, foram aprovadas novamente **1.073 verificações PHP**, **72 verificações JavaScript** e a integração no Apache, incluindo os assets versionados. A falha visual ainda não foi reproduzida pela ferramenta, que permanece sem navegador disponível.

Validação desta etapa: **1.073/1.073 verificações PHP** em cópia temporária; **59/59 verificações JavaScript** em V8 com DOM/armazenamento simulados; sintaxe dos **29 arquivos PHP** sem erros. Testes de contraste avaliam separadamente os dois temas, incluindo hover, botões e foco. Servidor PHP temporário confirmou home, catálogo e quatro detalhes 200; projeto oculto e rotas desconhecidas 404; CSS/scripts 200 com conteúdo íntegro. A consulta ao Apache em `/portifolio/` retornou 404, portanto essa instalação não foi validada nesta etapa.

Não havia navegador conectado: revisão visual mobile/desktop, teclado real, console e ausência de flash continuam pendentes. Não foi executado Lighthouse. O teste JavaScript pode ser repetido com Node.js, opcional apenas para desenvolvimento:

```sh
node tests/theme.js
```

Os comandos da suíte PHP permanecem na seção de verificação. A implementação não exige Node.js em produção. Histórico e limites em [PROGRESS.md](PROGRESS.md).

## Conteúdo aprovado — 19/10/2026

Povoamento baseado em `PORTFOLIO_CONTENT_GUIDE_ATUALIZADO.md` (nome disponível do guia solicitado como `PORTFOLIO_CONTENT_GUIDE.md`), currículo local e READMEs dos cinco repositórios ativos, com conferência dos trechos de código relevantes. O guia existente foi preservado. Não foram executadas as aplicações dos outros repositórios nem suas integrações.

- `config/profile.php`, consultado pelo Model `Profile`, concentra apresentação, formação, estágio, objetivos e as quatro certificações. A formação continua em andamento, com término previsto para dezembro de 2026. O estágio registra março a outubro de 2026 e o Arco Literário como experiência no IFMS, sem card de repositório pessoal. Certificações contêm apenas nomes.
- Cinco projetos cadastrados, quatro visíveis: Gerenciador de Permanências, Crowns, Festividades de Fim de Ano e Notícias. Gerenciador e Crowns aparecem em destaque. Os status editoriais são mantidos em `config/projects.php`, incluindo Crowns como descontinuado, preservando as descrições das limitações técnicas. Festividades e Notícias são secundários.
- Portfólio MVC em PHP está preenchido com `visible => false` e `featured => false` até a confirmação da publicação. Um projeto oculto é excluído de `all()`, `featured()` e `findBySlug()`; sua URL retorna 404. Após confirmar a publicação, habilite `visible` e selecione o destaque em `config/projects.php`.
- `status` é texto opcional, escapado nos cards e detalhes, para informar o estágio e as limitações do projeto. Não se confunde com `featured` ou `visible`.
- Festividades e Notícias têm links de demonstração cadastrados. A demo de Festividades, `https://heitorsv001.github.io/festividades/`, respondeu HTTP 200 na validação do povoamento; a demo de Notícias foi incluída pelo proprietário posteriormente. O formulário apenas simula o envio. Todas as imagens continuam nulas, com a área omitida; nenhuma ilustração foi apresentada como captura real.
- Habilidades agrupadas conforme currículo e projetos, sem porcentagens; GitHub e LinkedIn preenchidos. E-mail continua nulo, pois o guia exige autorização específica para divulgação; PDF de currículo não fornecido.

Validação do povoamento: **986/986** verificações com `E_ALL`, sintaxe dos **29 arquivos PHP** e `git diff --check` aprovados. Integração HTTP aprovada no Apache com `/portifolio` e em servidor PHP temporário com raiz `public/`: páginas 200, projeto oculto/rotas inválidas/POST 404, assets corretos, navegação e metadados válidos, arquivos privados inacessíveis. Servidor temporário encerrado. Navegador conectado indisponível; revisão visual responsiva, console, zoom e teclado do conteúdo preenchido não foram executados. Os resultados Lighthouse em `PROGRESS.md` pertencem à etapa 8 anterior.

Pendências técnicas documentadas: compilação e persistência do Crowns; testes de interação dos projetos; capturas reais; dados complementares dos certificados; domínio, hospedagem, `APP_ORIGIN`, publicação, robots/sitemap e preview público. A etapa de povoamento foi realizada sem commits ou pushes automáticos.

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
- **HTML, CSS e JavaScript próprio** para temas, sem framework de interface ou fontes remotas.
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

A produção usa a exportação estática no Cloudflare Workers em https://heitorvareiro.heitorsvareiro.workers.dev/, com `robots.txt` e `sitemap.xml` gerados no build. As instruções Apache acima se aplicam à execução PHP. Preview em redes sociais continua pendente.

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

Para o currículo, disponibilize um PDF aprovado em `public/assets/documents/` e informe um caminho relativo como `assets/documents/curriculo.pdf`. Nomes e subpastas aceitam letras ASCII, números, hífens e sublinhados. Arquivos inexistentes ou fora desse diretório são omitidos; o link de download só aparece quando o caminho é aceito. O arquivo fornecido pelo responsável, `CurriculoLinkedin.pdf`, está disponível em Contato.

GitHub e LinkedIn estão preenchidos com os perfis aprovados. O e-mail `heitorsvareiro@gmail.com` foi autorizado pelo responsável em 21/10/2026 e está disponível na seção de contato por link `mailto:`. O currículo fornecido está disponível no botão “Baixar currículo (PDF)”.

## Verificação e limites

Com as ferramentas instaladas, execute na raiz:

```sh
composer validate --no-check-publish
composer dump-autoload --optimize --strict-psr
php -d error_reporting=E_ALL tests/run.php
find app public config tests scripts -name '*.php' -print0 | xargs -0 -n1 php -l
php -l index.php
```

A suíte existente verifica roteamento, Controllers e Models, renderização, escaping, estados vazios, validação de links e arquivos, metadados e contraste de tokens. Ela cria fixtures temporárias, incluindo arquivos de imagem e documento, e os remove ao concluir. Para preservar integralmente uma cópia de trabalho, execute-a em uma cópia temporária.

Na revisão documental anterior ao povoamento, a suíte foi executada em cópia temporária com PHP 8.4.26 e aprovou **979/979 verificações**. Os **27 arquivos PHP** versionados passaram na checagem de sintaxe. Composer não estava disponível: instalação e validação do autoload não foram repetidas. Também não foram repetidos testes HTTP, Apache, Lighthouse ou inspeção visual no navegador.

[PROGRESS.md](PROGRESS.md) e [PROJECT_CONTEXT.md](PROJECT_CONTEXT.md) preservam o histórico de desenvolvimento e validações anteriores, incluindo o aceite manual da etapa 8 informado pelo proprietário em 18/10/2026. Esses registros são históricos; não representam novas execuções nesta revisão. A automação de publicação aguarda a ativação descrita acima.

## Imagens e apresentação

`public/assets/images/social-preview.png` e sua fonte SVG são artes para compartilhamento, não capturas de tela. O favicon também é um recurso de identidade visual. Capturas reais do próprio portfólio foram geradas na etapa 9 e estão em [design/validation/stage9](design/validation/stage9), com índice no [relatório](design/REDESIGN_VALIDATION.md). Elas não substituem as capturas ainda ausentes das aplicações apresentadas como projetos.

O conteúdo foi preenchido e publicado; a entrega automática ainda precisa ser validada na primeira execução autorizada. A estrutura MVC, as regras de apresentação e a suíte de verificações já estão implementadas.

## Autoria e repositório

Mantido na conta [Heitorsv001](https://github.com/Heitorsv001). Repositório original: [Heitorsv001/Portifolio](https://github.com/Heitorsv001/Portifolio).
