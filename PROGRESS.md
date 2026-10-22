# Progresso e passagem de contexto

## Currículo PDF disponibilizado em Contato — 21/10/2026

O responsável forneceu `public/assets/documents/CurriculoLinkedin.pdf`. Caminho cadastrado em `config/contact.php`, habilitando o botão “Baixar currículo (PDF)” já existente. Arquivo original de 44.592 bytes preservado; sem mudanças de layout, Models ou conteúdo profissional.

Validação: **1.601/1.601 verificações PHP** com `E_ALL` em cópia temporária; sintaxe da configuração e `git diff --check` aprovados. Home HTTP 200 com um único link de download; PDF HTTP 200 com MIME `application/pdf` e bytes iguais ao original. README/contexto sincronizados. Sem commit/push.

## Retrato renomeado, favicon e enquadramento — 21/10/2026

A pedido do responsável, arquivo renomeado para `public/assets/images/meu-retrato.png`, preservando seus bytes. Referência do avatar atualizada; imagem também declarada como favicon PNG pelo Controller/layout compartilhados, atendendo home, catálogo, detalhes e 404. Avatar com zoom de 15% e posição vertical de 40% por CSS, mantendo o círculo de 80/112px. Original sem edição; testes existentes e referências atuais de documentação adaptados.

Validação: **1.601/1.601 verificações PHP** com `E_ALL` em cópia temporária; sintaxe dos quatro arquivos PHP envolvidos e `git diff --check` aprovados. Chrome: quatro casos (390/1440px × claro/escuro), imagem carregada, zoom e recorte conferidos, sem overflow. Favicon confirmado na home, catálogo, detalhe e 404; PNG servido com HTTP 200, MIME e bytes corretos. Evidências em `design/validation/portrait-favicon/`; amostras abertas para revisão. Navegador temporário encerrado. Sem commit/push.

## Retrato autorizado incluído no início — 21/10/2026

O responsável forneceu `public/assets/images/Retrato Masculino com Luz Violeta.png` e solicitou sua inclusão. Substituído o símbolo neutro do avatar por essa imagem em `app/Views/home/index.php`, com URL codificada respeitando o caminho-base, texto alternativo, dimensões reais de 1122×1402 e prioridade de carregamento. CSS mantém o círculo em 80px no mobile e 112px no desktop, com `object-fit: cover` e enquadramento no rosto; arquivo original preservado. Expectativa antiga de avatar sem imagem adaptada na suíte existente.

Validação: **1.601/1.601 verificações PHP** com `E_ALL` em cópia temporária; sintaxe da view e dos testes e `git diff --check` aprovados. Chrome: quatro casos (390/1440px × claro/escuro), imagem carregada, círculo com medidas esperadas e ausência de overflow. Apache serviu a imagem com **HTTP 200**, MIME PNG e bytes iguais ao original. Capturas e verificações em `design/validation/portrait/`; amostras abertas para inspeção visual. Navegador temporário encerrado. Documentação identifica que a medição de desempenho da etapa 9 antecede essa inclusão. Sem commit ou push.

## Redesign — etapa 9 revisada e validada localmente em 21/10/2026

O responsável aprovou a etapa 8 e o rótulo “E-mail de contato”, autorizando a revisão final. Além da ferramenta de navegação sem sessão conectada, foi encontrado Chrome instalado no sistema, utilizado em modo headless por CLI/DevTools com perfis temporários próprios. Assim, esta etapa inclui verificações no navegador e capturas reais do portfólio.

- `public/assets/js/hero-particles.js`: permissão de movimento armazenada e atualizada nos eventos de mídia/resize, sem consultar preferências a cada quadro. Corrigido o caso observado em que o CSS ocultava o canvas após mudança de preferência, mas o buffer permanecia até resize. Conferidos liberação com movimento reduzido/toque e retomada após remoção da redução de movimento.
- `tests/particles.js`: teste de regressão para impedir consultas repetidas de mídia no ciclo de desenho. Cores, tamanho dobrado no claro, limites e comportamento de pausa preservados.
- `design/REDESIGN_VALIDATION.md` e `design/validation/stage9/`: relatório com critérios, limites, resultados, 30 PNGs e evidências JSON. Estado sincronizado em README, contexto e plano. Cinco arquivos de configuração comparados por SHA-256 permaneceram idênticos ao início da etapa; PHP MVC, Models, rotas, dados, textos e dependências preservados.

Validações atuais: **1.601/1.601 PHP** com `E_ALL` em cópia temporária; **72/72 de tema**, **29/29 de navegação**, **38/38 de partículas** em V8 com simulação. Sintaxe dos **30 arquivos PHP** e `git diff --check` aprovados.

Chrome 156.0.8078.47: **480/480 verificações** de matriz (quatro páginas × seis larguras × dois temas), **39/39 de interação** e **6/6 de desenho/partículas**. Sem overflow de 320 a 1440px nos casos avaliados; textos ampliados a 200% sem overflow nas quatro larguras testadas. Menor contraste calculado de texto: **6,38:1**. Conferidos skip link, Tab/Enter/Escape, foco, âncoras sob header, breakpoint, temas/persistência/sistema/armazenamento bloqueado, navegação sem JS e partículas com preferências em tempo real. Zero erros inesperados de console/rede; 12 mensagens de 404 esperadas nas visitas à rota de teste. Não foi testado zoom nativo ou tecnologia assistiva.

HTTP: **20 GET e dois POST** com status esperado nos dois caminhos-base; **10 URLs de assets** com 200/MIME/bytes corretos, atualizadas após o ajuste do script. Mais oito casos de layout no Chrome com `public/` como raiz. Home, catálogo e quatro detalhes 200; projeto oculto, slugs inválidos/desconhecidos, rota inexistente e método não registrado 404. GitHub pessoal, quatro repositórios e duas demos responderam 200. LinkedIn retornou 999 por bloqueio da consulta automatizada; link preservado. Nenhum envio de e-mail/formulário.

Amostras locais de performance: home FCP/LCP 108ms, catálogo 88ms, detalhe 84ms; CLS 0 e nenhuma tarefa longa na janela observada. Desenho medido em 25 quadros/1,1s, com pausa real fora do hero e em movimento reduzido. São amostras locais curtas, sem limitação de CPU/rede, não resultados Lighthouse ou garantia de desempenho em celular/hospedagem.

Chrome e servidor PHP auxiliar encerrados; Apache preservado. Aceite visual final do responsável, Lighthouse, zoom nativo e dispositivos/tecnologias assistivas permanecem pendentes. Foto, capturas reais das aplicações e currículo PDF continuam ausentes/opcionais. Publicação e ativação do card do próprio portfólio não realizadas. Nenhum commit/push.

## E-mail de contato disponibilizado — 21/10/2026

O responsável autorizou explicitamente o contato `heitorsvareiro@gmail.com`. Endereço cadastrado em `config/contact.php`, usando o link `mailto:` já previsto na seção de contato. Guia, contexto e documentação de contatos sincronizados com a autorização. Currículo permanece ausente; nenhum envio de e-mail executado.

Validação: **1.602/1.602 verificações PHP** com `E_ALL` em cópia temporária, sintaxe da configuração e `git diff --check` aprovados. Home **HTTP 200** no Apache, com endereço correto e um único link `mailto:` na seção de contato, sem PDF ou avisos PHP. Sem avanço de etapa, commit ou push.

## Redesign — etapa 8 implementada em 21/10/2026

O responsável aprovou os ajustes de redundância de Sobre mim e autorizou a próxima etapa. Etapa 7 aceita; implementados somente contato e rodapé.

- `app/Core/Controller.php`: disponibiliza `Contact::all()` ao layout quando a página não fornece contatos. Usa os dados já normalizados pelo Model, mantendo o valor injetado pelo HomeController para hero, contato e rodapé; sem alterar Controllers específicos ou Models.
- `app/Views/partials/footer.php`: nome/título em bloco discreto, `social-links.php` reutilizado e retorno ao topo. Mesmo rodapé em home, catálogo, detalhes e páginas de erro; redes ausentes não deixam listas vazias. Ícones de 22px e áreas de clique de 44px na fonte padrão, nomes acessíveis e foco existentes compartilhados com o hero.
- `app/Views/partials/contact.php` e `public/assets/css/style.css`: lista com semântica preservada e grid fluido, links com borda, superfície dos tokens e áreas de clique ampliadas. Rodapé flexível com quebra de linha e cores dos dois temas. Caminhos condicionais de e-mail e currículo mantidos; configuração real continua sem ambos.
- `tests/run.php`: verificações de rodapé nos caminhos-base já cobertos, contatos injetados sem substituição, URLs escapadas, nomes acessíveis, redes completas/parciais/ausentes, ausência de e-mail/PDF no rodapé, retorno ao topo e redes nos detalhes/erros por slug. Testes existentes de `mailto:` e PDF opcionais preservados.

Validação: **1.602/1.602 verificações PHP** com `E_ALL` em cópia temporária; **72/72 de tema**, **29/29 de navegação** e **37/37 de partículas** em V8 com simulação. Sintaxe dos **30 arquivos PHP** e `git diff --check` aprovados.

Apache: home, catálogo e quatro detalhes **200**; projeto oculto, slug inexistente e rota desconhecida **404**. Nove páginas com rodapé contendo as duas redes nomeadas e retorno ao topo, IDs únicos e sem e-mail/PDF ou avisos PHP. Home com links de contato esperados e parágrafo redundante do estágio ausente. CSS versionado **200**, MIME e conteúdo corretos.

Ferramenta sem apps/navegadores conectados (`apps: []`, `browsers: []`). Revisão visual da etapa 8 em ambos os temas e larguras previstas, reflow/zoom, teclado, console e screenshots permanecem pendentes; não executado Lighthouse. Dados profissionais e ajustes editoriais anteriores preservados. Etapa 9 não iniciada; nenhum commit/push.

## Revisão de redundâncias dos textos — 21/10/2026

A pedido do responsável, removido de Sobre mim o parágrafo sobre o estágio que repetia Experiência profissional. A introdução foi reduzida à apresentação do trabalho com aplicações web e desktop, sem repetir a condição acadêmica, o estágio ou a lista de tecnologias. O parágrafo de projetos mantém tecnologias e práticas, sem repetir os planos de evolução, organização de código e compreensão das decisões técnicas apresentados em Objetivos profissionais. Título, condição de estudante, experiência completa, formação, certificações e objetivos preservados. Guia de conteúdo sincronizado.

Também revisados os textos de projetos, habilidades, contato, rodapé e páginas internas. Resumos dos cards e descrições de detalhes cumprem funções de síntese e aprofundamento; listas de tecnologias e identificação acadêmica nas respectivas seções continuam úteis à leitura. Nenhuma alteração adicional nessas áreas.

Validação: **1.356/1.356 verificações PHP** com `E_ALL` em cópia temporária, sintaxe de `config/profile.php` e `git diff --check` aprovados. A redução de três verificações acompanha a remoção de mais um parágrafo testado nos três caminhos-base. Alteração editorial, sem mudanças de layout, arquitetura ou avanço de etapa; nenhum commit/push.

## Parágrafo redundante removido de Sobre mim — 21/10/2026

A pedido do responsável, removido apenas o primeiro parágrafo de `profile.about`, que repetia a condição de estudante e a apresentação profissional já exibidas em Sobre mim. Introdução, título, condição acadêmica e demais parágrafos preservados. Guia de conteúdo sincronizado e verificação existente de estudante adaptada ao texto mantido.

Validação: **1.359/1.359 verificações PHP** com `E_ALL` em cópia temporária, sintaxe de `config/profile.php` e `tests/run.php` e `git diff --check` aprovados. A contagem diminuiu em três verificações porque o laço de preservação de conteúdo testa um parágrafo a menos em cada caminho-base. Sem avanço de etapa ou commit/push.

## Redesign — etapa 7 implementada em 21/10/2026

O responsável confirmou a etapa 6 e autorizou a próxima. Implementadas somente habilidades, formação e certificações, conforme o plano.

- `app/Views/partials/skills.php`: SVG decorativo por categoria junto de cada nome, mantendo as oito categorias e os 29 itens na ordem original. Ícones locais sem dependência ou download, ocultos de leitores de tela; nomes sempre disponíveis como texto, inclusive sem SVG. Categoria desconhecida recebe símbolo genérico.
- `app/Views/home/index.php`: formação retirada de dentro de Sobre e apresentada em `#formacao`, após habilidades e antes de contato. Curso, IFMS, período com previsão, situação “Em andamento” e os quatro nomes de certificações vêm das mesmas chaves do perfil. Biografia e objetivos mantidos; nenhum emissor, data ou competência acrescentado.
- `public/assets/css/style.css`: ícones com tamanho estável, alinhamento dos nomes e cores dos tokens; estilos de formação e lista discreta de certificações. Removidos estilos da ficha antiga de formação. `app/Views/partials/contact.php` teve apenas o número da seção alterado de 05 para 06.
- `tests/run.php`: presença e ordem de formação/certificações, preservação dos dados, nomes independentes dos ícones, escaping e inclusão de categorias futuras pela configuração. Documentação de estado sincronizada em README, contexto e plano.

Validações executadas: **1.362/1.362 verificações PHP** com `E_ALL` em cópia temporária; regressões JavaScript em V8 com simulação: **72/72 de tema**, **29/29 de navegação** e **37/37 de partículas**. Sintaxe dos **30 arquivos PHP** e `git diff --check` aprovados. Sem alteração de configurações, Controllers, Models, rotas ou dependências nesta etapa.

Apache: home, catálogo e quatro detalhes **200**; portfólio oculto e rota inexistente **404**. Conferidos oito grupos, 29 nomes/ícones, quatro certificações, valores de formação, ordem habilidades → formação → contato, IDs únicos e âncora de formação nas páginas internas. CSS versionado **200**, MIME e conteúdo corretos.

Ferramenta sem apps/navegadores conectados (`apps: []`, `browsers: []`). Revisão visual da etapa 7 nos dois temas e larguras previstas, reflow/zoom, teclado e console reais permanecem pendentes. Nenhum screenshot ou Lighthouse executado. Etapa 8 não iniciada; nenhum commit/push.

## Conteúdo do Gerenciador de Permanências atualizado — 21/10/2026

O responsável confirmou duas funcionalidades: geração de relatórios PDF com a relação de inscritos em cada permanência e envio automático de e-mail confirmando a inscrição. Atualizados resumo e descrição em `config/projects.php`, refletidos na home, catálogo e detalhe. Retirada a ressalva editorial antiga dessas integrações na apresentação, preservando os demais dados, status, links e limitações. Guia de conteúdo sincronizado com a confirmação do responsável, distinguindo-a de testes de integração pelo agente.

Validação: **1.326/1.326 verificações PHP** com `E_ALL` em cópia temporária; sintaxe de `config/projects.php` e `git diff --check` aprovados. As três páginas responderam **200** no Apache e exibiram os novos textos; ressalva antiga ausente do detalhe. Não foram executadas geração real de PDF ou entrega de e-mail do projeto externo. Sem commit/push ou avanço de etapa do redesign.

## Redesign — etapa 6 implementada em 21/10/2026

O responsável confirmou a etapa 5 e autorizou a próxima. Implementados somente projetos e integração dos detalhes, conforme o plano; habilidades e formação continuam para a etapa 7.

- `app/Controllers/HomeController.php`: uma consulta a `Project::all()` e separação dos registros já visíveis em destaques e secundários por `featured === true`. Ordem preservada; Model, normalização e regra de visibilidade intactos.
- `app/Views/home/index.php`: seção de projetos ocupa a largura de conteúdo; grupos “Em destaque” e “Outros projetos” com hierarquia de headings própria. Estado sem destaque preservado; grupo secundário omitido quando vazio. Catálogo completo continua acessível fora do hero.
- `app/Views/partials/project-list.php`: um único markup com apresentações `featured`, `compact` e `catalog`; reutilizados partials de tecnologias e links. Títulos h4 nos grupos da home e h3 no catálogo. Status/resumo escapados. Imagem real só nos layouts amplos/catálogo; lista compacta prioriza texto, mantendo a mídia acessível nas outras páginas. Sem imagem, nenhum espaço ou coluna fictícia.
- `app/Views/projects/index.php`: catálogo preservado com quatro itens e link contextual para a home. `app/Views/projects/show.php`: retorno principal para `/#projetos` e link de catálogo completo; conteúdo integral, opcionais e ressalvas preservados.
- `public/assets/css/style.css`: lista de destaques ampla, imagem lateral a partir de 64rem quando disponível, empilhamento no mobile, lista secundária compacta e catálogo em grid fluido. Tags, bordas e estados de foco/hover usam tokens dos dois temas. Detalhes com subtítulos menores e retornos que quebram linha quando necessário.
- `tests/run.php`: classificação em grupos, ausência de duplicação, projeto secundário sem opcionais, estados sem destaques, imagens válidas/inválidas, escaping, projeto oculto nos dois grupos e retorno principal do detalhe à home.

Validações executadas: **1.326/1.326 verificações PHP** com `E_ALL` em cópia temporária; regressões JavaScript em V8: **72/72 de tema**, **29/29 de navegação** e **37/37 de partículas**. Sintaxe dos **30 arquivos PHP** e `git diff --check` aprovados. Arquivos de dados de projetos, habilidades e contatos, Models, ProjectController, ErrorController, rotas e dependências sem alterações nesta etapa.

Apache: home, catálogo e quatro detalhes **200**; portfólio oculto e rota inexistente **404**. Parser de HTML confirmou home com Gerenciador/Crowns em `featured` e Festividades/Notícias em `compact`, sem duplicação, sem imagem fictícia e com as duas demos. Catálogo com quatro itens; detalhes com retorno à home, catálogo e descrição completa. IDs únicos, projeto oculto ausente do HTML, CSS/scripts versionados **200**, MIME/bytes corretos. Demos já cadastradas de Festividades e Notícias verificadas via GET: ambas **HTTP 200**. Isso comprova disponibilidade da página, não execução/teste das interações dessas aplicações.

Ferramenta novamente sem apps/navegadores conectados: não foram executados screenshots, revisão visual dos dois temas, responsividade real, teclado, zoom, console ou Lighthouse. Conferir cards sem imagem, mídia lateral com futura captura real, lista compacta e navegação dos detalhes nas larguras previstas. Imagens reais continuam pendentes de fornecimento/autorização.

Conteúdo profissional, `config/projects.php`, regras de visibilidade e slugs preservados. Nenhum commit/push. Etapa 7 não iniciada.

## Redesign — etapa 5 implementada em 21/10/2026

O responsável aprovou os ajustes das partículas e autorizou a próxima etapa. Aceite da etapa 4 registrado com base nessa confirmação, sem nova medição visual/gráfica pelo agente. Implementada somente a etapa de Experiência e Sobre.

- `app/Views/home/index.php`: seção `#experiencia` imediatamente após o hero, com heading próprio, período, cargo de estágio, instituição, Arco Literário, descrição integral e tecnologias em tags. Sobre mantém título profissional, condição acadêmica, introdução e todos os parágrafos da biografia. Objetivos integrados a Sobre com `#objetivos` e `#objetivos-titulo` preservados. Formação e certificações mantidas até a etapa 7. Retirada apenas a ficha duplicada de experiência de dentro de Sobre, sem retirar o parágrafo biográfico sobre o estágio.
- `config/profile.php`: único acréscimo é `experience.technologies` com Node.js, Express, MongoDB, EJS e Bootstrap 5, já citados na descrição/guia. Comparação do array com uma cópia anterior à etapa, removendo apenas o novo campo, confirmou igualdade integral dos valores anteriores. Nenhuma data, descrição, status, objetivo ou competência foi reescrita.
- `public/assets/css/style.css`: composição editorial do estágio, período monoespaçado e tags com estilos compartilhados; meta acima do texto no mobile/tablet e coluna lateral a partir de 64rem. Objetivos recebem espaçamento e margem de rolagem para o header. Temas, hero e partículas não foram alterados nesta etapa.
- `tests/run.php`: ordem hero → experiência → Sobre, presença de todos os dados do estágio e tecnologias, ausência de ficha repetida em Sobre, preservação dos objetivos/formação/certificados e escaping das novas tags.

Validações executadas: **1.297/1.297 verificações PHP** com `E_ALL` em cópia temporária; **72/72 de tema**, **29/29 de navegação** e **37/37 de partículas** em V8 com DOM/Canvas/eventos simulados. Sintaxe dos **30 arquivos PHP** e `git diff --check` aprovados. Apache: home, catálogo e quatro detalhes **200**; projeto oculto e rota inexistente **404**. Conferidos ordem, período do estágio, cargo, projeto, cinco tags, objetivos dentro de Sobre, formação/certificações, condição de estudante, IDs únicos e seis destinos de navegação. CSS e scripts versionados **200**, conteúdo/MIME corretos.

Navegador da ferramenta novamente indisponível (`apps: []`, `browsers: []`). Permanecem pendentes inspeção visual nos dois temas e larguras previstas, leitura da coluna de período, reflow/zoom, teclado real e foco dos destinos. Não foram executados screenshots ou Lighthouse. O aceite da etapa 4 não representa medição de desempenho gráfico pelo agente.

Models, Controllers, Router, projetos, habilidades, contatos, dependências, servidor e permissões preservados. Sem commit/push. Etapa 6 não iniciada.

## Partículas mais escuras e maiores no tema claro — 21/10/2026

A pedido do responsável, roxo escurecido para `#5b21b6` e tamanho das bolinhas dobrado no tema claro por `--particle-size-scale: 2`. Tema escuro mantém cor e tamanho originais (escala 1). A troca de tema aplica cor e escala sem reiniciar o efeito; conexões preservam espessura e opacidade. **37/37 verificações de partículas** em V8 com Canvas simulado e `git diff --check` aprovados, incluindo diâmetro dobrado no claro e tamanho original ao retornar ao escuro. Sem commit/push.

## Cor roxa das partículas — 21/10/2026

A pedido do responsável, `--color-particle` do tema claro passou para o roxo vibrante `#7c3aed`, substituindo o tom escuro anterior. Tema escuro, opacidades e movimento preservados. Fixture de cor atualizada; 35/35 verificações de partículas em V8 com Canvas simulado e `git diff --check` aprovados. Sem commit/push.

## Contraste das partículas no tema claro — 21/10/2026

A pedido do responsável, partículas e conexões agora usam `--color-particle`: `#211b2d` no tema claro, mais escuro que o cinza anterior; tema escuro preservado em `#b6b2c1`. Textos, opacidades e animação permanecem iguais. Alterados CSS, leitura do token no script e fixture de cor do teste existente. **35/35 verificações de partículas** aprovadas em V8 com Canvas simulado; revisão visual pelo agente não executada por falta de navegador conectado. Sem commit/push ou avanço de etapa.

## Redesign — etapa 4 implementada em 21/10/2026

O responsável confirmou que o hero estava certo e autorizou prosseguir. Aceite da etapa 3 registrado com base nessa confirmação, sem nova revisão visual pelo agente. Implementadas somente partículas; Experiência/Sobre permanecem para a etapa 5.

- `app/Views/home/index.php`: canvas decorativo no hero com `aria-hidden`, sem foco ou conteúdo semântico. Textos e demais blocos intactos.
- `app/Core/Controller.php` e `app/Views/layouts/main.php`: URL do novo script com versão pelo conteúdo, carregado com `defer` exclusivamente na home. Catálogo, detalhes e 404 não carregam o efeito.
- `public/assets/css/style.css`: canvas absoluto atrás do conteúdo, sem eventos de ponteiro; contexto de empilhamento próprio no hero. Canvas oculto no mobile, ponteiro impreciso e movimento reduzido. Nenhuma alteração das dimensões ou alinhamento do hero aceito.
- Novo `public/assets/js/hero-particles.js`: Canvas 2D sem dependências, até 64 pontos proporcionais à área, conexões a distâncias curtas e baixa opacidade; movimento de até 3px/s por eixo, afastamento suave ao mouse limitado a 8px. Teto de desenho de 30fps, DPR até 2 e buffer de aproximadamente 6 milhões de pixels.
- Efeito desativado abaixo de 56.25rem ou sem ponteiro preciso, e com `prefers-reduced-motion`. Alterações de mídia durante a visita cancelam/retomam a animação. Aba oculta e hero fora da viewport pausam `requestAnimationFrame`; retomada preserva um único loop. `IntersectionObserver` com fallback de rolagem, observação de tamanho e atualização de cores por mudança de `data-theme`. Falha/ausência de Canvas ou APIs essenciais deixa o fundo sem animação e o conteúdo utilizável.
- Novo `tests/particles.js`: limites de pontos/buffer/DPR/frequência, pausa/retomada, preferências iniciais/dinâmicas, tema, reação de mouse, ausência de reação ao toque e falhas de API. `tests/run.php`: canvas decorativo, script versionado com caminho-base/escaping e efeito ausente das páginas internas.

Verificações efetivamente executadas: **1.251/1.251 PHP** com `E_ALL` em cópia temporária; **72/72 de tema**, **29/29 de navegação** e **35/35 de partículas** em V8 com DOM/Canvas/eventos simulados; sintaxe dos **30 arquivos PHP** e `git diff --check` aprovados. Apache: home, catálogo e quatro detalhes **200**; projeto oculto e rota inexistente **404**. Canvas e script presentes somente na home; CSS e três scripts versionados **200**, MIME e bytes corretos.

Ferramenta novamente sem apps/navegadores conectados. Não foram executados screenshots, teclado/seleção/rolagem reais, medição de FPS, comparação gráfica com efeito desligado, inspeção de tarefas longas ou Lighthouse. Os limites e o ciclo de animação foram verificados por simulação; não representam medição de fluidez/bateria no dispositivo do responsável. Revisar intensidade e legibilidade nos dois temas, reação ao mouse e desempenho em desktop; confirmar ausência do efeito no mobile e com movimento reduzido.

Dados profissionais, Controllers, Models, Router, dependências, configuração de servidor e permissões preservados. Sem commit/push. Etapa 5 não iniciada.

## Redesign — etapa 3 implementada em 21/10/2026

O responsável informou que realizou a revisão visual da etapa 2, confirmou que estava tudo certo e autorizou o próximo passo. Esse aceite é do responsável; não houve execução visual pelo agente. Implementado somente o novo hero, sem antecipar partículas ou reorganização da experiência.

- `app/Views/home/index.php`: avatar circular neutro com símbolo de código decorativo, nome central, subtítulo exato “Desenvolvedor Web e estudante” e redes sociais. Removidos os CTAs do hero; título Full Stack, condição acadêmica e introdução realocados para Sobre mim com escaping, sem alterar nenhum valor do perfil. Biografia, objetivos e demais dados continuam presentes.
- Novo `app/Views/partials/social-links.php`: GitHub/LinkedIn condicionais, URLs normalizadas recebidas do HomeController, nomes acessíveis, SVGs decorativos locais e áreas de acionamento de 44px. Sem contatos, não renderiza uma lista vazia. Não inclui e-mail ou currículo no hero. Reutilização no rodapé permanece para a etapa 8.
- `app/Views/layouts/main.php`: home com classe própria e largura independente; demais páginas mantêm o contêiner original.
- `public/assets/css/style.css`: hero central, avatar de 80px no mobile e 112px no desktop, nome fluido e subtítulo menor. Mínimo de 75svh no mobile, viewport descontando header entre 900 e 1199px, e 100svh com espaço para header fixo acima de 1200px; fallbacks em vh. Uso de altura mínima permite crescer em viewports curtas. Demais seções continuam em contêiner de 1100px; sem efeitos ou animações nesta etapa.
- Não foi necessário alterar o Controller base ou o HomeController: o hero já recebe `Contact::all()` pelo fluxo existente. A preparação de contatos compartilhados para páginas internas permanece para a etapa do rodapé, evitando dependência sem uso nesta etapa. Nenhuma foto ou campo biográfico novo foi adicionado.
- `tests/run.php`: subtítulo exato, ausência de CTA/foto fictícia, redes acessíveis e escapadas, estado sem contatos, conservação dos textos em Sobre e separação do layout de home/internas.

Verificações: **1.232/1.232 PHP** com `E_ALL` em cópia temporária; **72/72 de tema** e **29/29 de navegação** em V8 com DOM/eventos simulados; sintaxe dos **30 arquivos PHP** e `git diff --check` aprovados. Apache: home, catálogo e quatro detalhes **200**; projeto oculto e rota inexistente **404**. Na home, confirmados nome, subtítulo, fallback e exatamente duas redes no hero, sem botões, e-mail, currículo ou IFMS no subtítulo. IDs únicos e layout próprio das páginas internas conferidos; CSS/scripts versionados **200**, bytes e MIME corretos.

Ferramenta continua com `apps: []`, `browsers: []`: não foram executadas revisão visual, teclado real, zoom, console ou screenshots desta etapa. Validar composição e altura em desktop, mobile e viewport curta, foco das redes, dois temas e convivência com o header. Foto pessoal segue pendente de fornecimento/autorização; o fallback é a entrega atual. Não foi executado Lighthouse.

Configurações de conteúdo, Models, Controllers, Router, dependências, servidor e permissões preservados. Nenhum commit/push. Etapa 4 (partículas) não iniciada.

## Redesign — etapa 2 implementada em 21/10/2026

O responsável confirmou “tudo certo” após a correção do botão de tema e autorizou a próxima etapa. Esse aceite é do responsável, sem teste visual pelo agente. Implementados somente header e navegação conforme a etapa 2 do plano.

- `app/Views/partials/header.php`: marca com indicação de página atual na home, controles de tema/menu agrupados e seis destinos (Experiência, Sobre, Projetos, Habilidades, Formação e Contato). Hambúrguer com `aria-expanded`, `aria-controls`, rótulos de abrir/fechar e ícones decorativos. Link de catálogo preservado na seção de projetos; nenhuma rota removida.
- `app/Views/home/index.php`: somente IDs e `tabindex="-1"` nos títulos existentes de formação e experiência para os links funcionarem já nesta etapa. Nenhum dado ou bloco foi movido, apagado ou reescrito.
- `public/assets/css/style.css`: header aderente, home transparente no topo desktop e opaca ao rolar, fundo sólido nas internas; painel compacto não modal, controles com mínimo de 44px, contêiner flexível e margem de rolagem baseada na altura medida do header. Breakpoint de 75rem para acomodar nome completo, controles e seis links; em 1024px o menu é compacto. Sem animação nova, bloqueio de rolagem ou alteração de cursor fora dos controles.
- `public/assets/js/site.js`: abertura/fechamento, Escape com retorno de foco, seleção com foco no destino local, fechamento por clique externo/saída de foco, transição de breakpoint sem foco em controles ocultos e atualização de altura/estado ao rolar. Inicialização da navegação independente da do tema. Sem JS/API de breakpoint, links continuam visíveis e o hambúrguer fica oculto.
- `tests/run.php`: contratos do menu, âncoras nas páginas internas e semântica; retiradas expectativas antigas de link de catálogo marcado como página atual, pois o menu agora aponta à seção da home. `tests/theme.js` adaptado aos seletores da nova inicialização. Novo `tests/navigation.js` cobre comportamento com DOM/eventos simulados, sem substituir teste de teclado real.

Verificações executadas: **1.181/1.181 PHP** com `E_ALL` em cópia temporária; **72/72 de tema** e **29/29 de navegação** em V8, executando os arquivos reais; sintaxe dos **29 arquivos PHP** e `git diff --check` aprovados. HTTP no Apache: home, catálogo e quatro detalhes **200**; projeto oculto e rota inexistente **404**. Confirmados IDs únicos, relação do botão com o painel, seis destinos existentes na home, navegação visível no HTML sem JS, classe da home restrita à home e três assets versionados **200**, MIME/bytes corretos.

Ferramenta de navegador retornou novamente `apps: []`, `browsers: []`. Permanecem pendentes revisão visual em 360, 390, 768, 1024 e 1440px nos dois temas, teclado real (Tab/Shift+Tab, Enter/Espaço e Escape), foco não encoberto, zoom, console e rolagem em viewport curta. Não foram geradas screenshots nem executado Lighthouse nesta etapa. Estado aberto/fechado e foco foram testados por eventos simulados, não por interação real no navegador.

Configurações profissionais, Models, Controllers, rotas, dependências e servidor preservados. Nenhum commit/push. Etapa 3 (hero) não iniciada; aguardar nova solicitação.

## Correção do controle de tema — 21/10/2026

Relato do responsável: botão fino, ícones quase invisíveis e ausência de mudança de cores na página inicial. Sem navegador conectado, não foi possível reproduzir visualmente o sintoma nem confirmar sua causa. Os arquivos entregues pelo Apache correspondiam aos arquivos locais; cache antigo no cliente permanece uma hipótese, não uma causa comprovada.

- Botão com largura/altura mínimas de 44px no tamanho de fonte padrão, base flex fixa e sem padding que comprima os ícones; SVGs de 22px sem encolhimento e traço de 2px, com dimensões também declaradas no HTML.
- `app/Core/Controller.php` compõe URLs de CSS e scripts com versão baseada no SHA-256 do conteúdo. Alterações nesses arquivos produzem novas URLs, evitando reaproveitamento de uma versão anterior em cache. Layout usa essas URLs com escaping e mantém o script de inicialização anterior ao CSS.
- A meta `theme-color` acompanha o fundo definido nos tokens. Sugere cor à barra dos navegadores que a suportam; não altera configurações ou tema global do navegador. A alternância principal continua aplicando as cores à página.
- Testes atualizados para URLs versionadas, caminhos-base, escaping e sincronização da cor sugerida. **1.073/1.073 verificações PHP** em cópia temporária e **72/72 verificações JavaScript** em V8 com DOM simulado aprovadas; sintaxe dos **29 arquivos PHP** e `git diff --check` aprovados.
- Apache agora confirmado em `/portifolio/`: home, catálogo e detalhe Crowns **200**; projeto oculto e rota desconhecida **404**. As três URLs versionadas retornam **200**, MIME correto e bytes idênticos aos arquivos locais.
- Tentativa de abrir a página pelo navegador da ferramenta retornou `Browser is not available: iab`. Tamanho e interação reais ainda precisam de confirmação visual; não foi alegada reprodução da falha. Nenhuma alteração em conteúdo, Models, rotas, servidor ou permissões; sem commit/push.

## Redesign — etapa 1 implementada em 21/10/2026

Pedido: executar a próxima etapa de `design/REDESIGN_PLAN.md`. Implementados somente tokens e temas. Reconsultados AGENTS, contexto, guia de conteúdo, plano e guia visual. Nesta execução, o guia e a captura já estavam em `design/`, em vez de `config/design/`; nenhuma movimentação foi realizada pelo agente.

- `public/assets/css/style.css`: paleta do guia para escuro/claro e variantes de hover, foco, texto sobre acento; tipografia monoespaçada em rótulos e tecnologias, contêiner de 1100px, padding de 20/24px, raio de 12px e entrelinha 1,7. Todas as cores dos componentes continuam por tokens.
- `app/Views/layouts/main.php`: script externo de inicialização anterior ao CSS e interações com `defer`, ambos com caminho-base escapado. SEO, skip link e layout MVC preservados.
- `app/Views/partials/header.php`: botão nativo de 44 × 44px com ícones decorativos, foco compartilhado e nome/tooltip que anunciam o tema de destino. Fica oculto até o script habilitar o controle.
- Novos `public/assets/js/theme-init.js` e `site.js`: preferência salva válida, sistema e fallback escuro; tratamento de falha do armazenamento e de `matchMedia`; mudanças do sistema sem sobrepor escolha manual. Sem JS, tema escuro e navegação existente continuam disponíveis.
- `tests/run.php`: contraste separado por tema/superfície, presença/ordem/caminho-base dos scripts, escaping e controle em todas as páginas. Novo `tests/theme.js`: comportamento da preferência, alternância, persistência entre páginas, rótulos, falhas e APIs de sistema ausentes/legadas.
- README, contexto e plano atualizados com o estado desta etapa. Nenhuma mudança em dados profissionais, Models, Controllers, rotas, dependências, configuração Apache ou permissões. Novo hero/menu não implementados. Nenhum commit ou push.

Verificações efetivamente executadas:

- **1.073/1.073** verificações PHP com `E_ALL` em cópia temporária, sem fixtures na árvore original.
- **59/59** verificações JavaScript em V8 pelo executor de ferramentas, com DOM e armazenamento simulados, executando os arquivos reais. `node` não estava disponível no shell; não foi instalado. O mesmo arquivo pode ser executado com `node tests/theme.js` em ambiente com Node.js.
- Sintaxe dos **29 arquivos PHP** aprovada. Contraste dos tokens de texto/links/foco sobre fundo, superfície e hover, além dos botões, aprovado nos dois temas.
- Servidor PHP temporário em `127.0.0.1:8026`, `APP_BASE_PATH=''` e raiz `public/`: home, catálogo e quatro detalhes **200**; próprio portfólio oculto, slug desconhecido e rota desconhecida **404**. Todas as respostas com HTML executado, um controle e scripts corretos; CSS e os dois scripts **200**, bytes idênticos aos locais e MIME correto. Sem CSP retornada nesse servidor. Caminhos-base diferentes e escaping cobertos pela suíte PHP.
- Apache em `http://localhost/portifolio/`: **404**, sem validação da aplicação nessa instalação. Nenhuma tentativa de alterar o servidor. A verificação HTTP em Python precisou de execução fora do sandbox por bloqueio de sockets.
- Ferramenta de navegador retornou `apps: []`, `browsers: []`. Não foram executados testes visuais, teclado real, console, zoom, flash ou Lighthouse. O carregamento do script antes do CSS foi verificado no HTML; isso não comprova ausência visual de flash.
- `git diff --check` aprovado; dados, Models, Controllers, Core, front controllers e configuração sem diferenças. Servidor PHP temporário encerrado após as verificações.

Aceite automatizado e integração no servidor PHP aprovados; aceite visual da etapa 1 permanece pendente. Conferir home, catálogo, detalhe e 404 nos dois temas e em mobile/desktop, foco/ativação por teclado e recargas antes de considerar o aceite integral. Não avançar automaticamente para a etapa 2.

---

Atualizado em **19/10/2026**. A seção 0 registra o povoamento autorizado. As demais seções preservam o histórico e devem ser interpretadas como estados anteriores. Conferir novamente o ambiente na próxima sessão.

## 0. Estado atual — conteúdo preenchido, publicação pendente

Atualização solicitada pelo proprietário em 19/10/2026: Gerenciador de Permanências incluído nos destaques junto de Crowns. As cinco fichas passaram ao status “Finalizado · fora de produção”. Retirada a revisão de segurança como condição para o destaque, pois a solicitação trata da apresentação dos projetos, sem deploy. Mantidas as limitações funcionais verificadas e a ocultação do próprio portfólio até a publicação. Alterados nesta atualização: `config/projects.php`, `README.md`, `PROJECT_CONTEXT.md` e este registro. Nenhum commit ou push.

Validação desta atualização: `php -l config/projects.php` sem erros; **986/986** verificações com `E_ALL`; `git diff --check` aprovado. Conferidos também os cinco status, a seleção de Gerenciador/Crowns e a renderização da home com dois cards em destaque. Os registros de povoamento e testes abaixo descrevem a entrega anterior a essa atualização editorial.

Pedido executado: povoamento do MVC existente conforme o guia atualizado, sem commits. Lidos `AGENTS.md`, `PROJECT_CONTEXT.md`, este registro, README e `PORTFOLIO_CONTENT_GUIDE_ATUALIZADO.md`. O nome solicitado `PORTFOLIO_CONTENT_GUIDE.md` não existe; foi utilizado o arquivo atualizado já presente e não rastreado no início da tarefa, sem modificá-lo.

### Conteúdo e integração

- `config/profile.php` e Model `Profile` acrescentados para fornecer nome, título, biografia, formação, estágio, certificações e objetivos ao `HomeController`. Formação explicitamente em andamento (janeiro/2024 a dezembro/2026, previsão); estágio de março a outubro/2026, sem presumir continuidade. Arco Literário consta como estágio no IFMS, sem card ou link de código privado. Quatro certificações, somente nomes.
- Atualizados `config/app.php`, `config/contact.php` e `config/skills.php`. GitHub/LinkedIn aprovados, sem e-mail público ou PDF; conhecimentos conforme as fontes, sem percentuais. Atualizados home, footer e texto introdutório do catálogo.
- `config/projects.php`: cinco fichas, usando apenas repositórios ativos e os nomes públicos do guia. Quatro projetos visíveis. Portfólio MVC em PHP oculto até confirmar publicação; Gerenciador fora dos destaques enquanto não houver revisão de segurança, preservando a condição mais restritiva do guia. Crowns em destaque com persistência parcial; Festividades e Notícias secundários.
- `Project` filtra `visible => false` antes de disponibilizar catálogo, destaques ou consulta por slug; a rota direta do projeto oculto retorna 404. Campo opcional `status` mostra ressalvas em cards e detalhes. Dados antigos sem `visible` continuam visíveis por padrão. Imagens ausentes omitidas; removidos o placeholder e seu CSS sem uso.
- Sem alterações no Router, Core, dependências, Apache ou permissões. Nenhum `git add`, commit ou push executado. README e contexto atualizados.

### Fontes e conferência

Currículo lido em `/home/heitor/Documentos/CurriculoLinkedin.md`; READMEs dos cinco repositórios lidos em `/home/heitor/github-projects/repositorios-github/`. Conferidos os Controllers de login, permanências e alunos do Gerenciador, Maven e Controller de batalha do Crowns, scripts de Festividades e Notícias e implementação atual do próprio portfólio. As limitações documentadas são compatíveis com os trechos inspecionados; não houve nova auditoria integral ou execução dessas aplicações. Nenhuma credencial ou dado interno foi copiado para o site.

O README de Festividades confirma que o tema é Ano Novo Chinês e indica a demo `https://heitorsv001.github.io/festividades/`; requisição GET nesta sessão retornou **200**. Link incluído, mantendo o aviso de formulário simulado, sem armazenamento/envio real e com campos na URL. Não foram inventadas demos nem capturas.

### Testes desta entrega

- Antes das mudanças: **979/979** verificações com `E_ALL`.
- Após as mudanças: **986/986** com `php -d error_reporting=E_ALL tests/run.php`. Expectativas de placeholders atualizadas; novos testes para ocultação mesmo com `featured` ativo, 404 por URL direta, reativação via configuração e escaping do perfil/ressalvas. Fixtures temporárias, sem modificar dados reais.
- Sintaxe dos **29 arquivos PHP** (`app`, `public`, `config`, `tests` e entrada raiz): sem erros. `git diff --check` aprovado.
- Apache em `http://localhost/portifolio` e servidor PHP temporário em `http://127.0.0.1:8026` com `public/` como raiz: home, catálogo e quatro detalhes **200**; projeto oculto, slug desconhecido/inválido, segmento extra, rota inexistente e POST ao detalhe **404**. Autoload Composer existente funcionou com `Profile`.
- HTML conferido nos dois servidores: h1/IDs únicos, destinos de âncoras e referências de headings válidos, metadados consistentes, quatro cards no catálogo e apenas Crowns em destaque, sem projetos excluídos, e-mail ou placeholders de imagens. CSS/favicon/preview **200**, bytes iguais aos arquivos locais. Configurações e View privadas **403** no Apache e **404** com raiz pública.
- Nenhuma reinstalação/alteração do Composer. Servidor temporário encerrado após a verificação.
- Inventário da ferramenta: `apps: []`, `browsers: []`. Revisão visual responsiva, console, teclado e zoom do conteúdo novo não executados; não foi repetido Lighthouse. O aceite da etapa 8 abaixo pertence ao layout anterior e não equivale a uma nova inspeção visual.

### Pendências

Revisão de segurança do Gerenciador antes de destaque/demo; compilação e persistência do Crowns; interações de Festividades/Notícias e responsividade dessas aplicações; capturas reais; dados complementares dos certificados; autorização específica do e-mail e PDF aprovado. Para o portfólio: revisão visual do conteúdo preenchido, domínio/hospedagem, `APP_ORIGIN`, publicação, habilitação do próprio projeto, robots/sitemap e preview público. Não habilitar demonstração do Gerenciador sem revisão nem declarar estágio ou formação concluída além das datas documentadas. Nenhum deploy realizado.

## Histórico — etapa 8 concluída

Em **18/10/2026**, o proprietário confirmou que realizou os testes e que a responsividade e a acessibilidade estão corretas. A pendência de aceite manual da etapa 8 foi encerrada com base nessa confirmação, complementando as auditorias Lighthouse e os testes automatizados registrados abaixo. Os testes manuais foram realizados pelo proprietário, sem nova execução pelo agente.

- Etapa 8 marcada como concluída em `PROJECT_CONTEXT.md`; README atualizado para refletir o aceite.
- Alterados somente os documentos de acompanhamento, sem mudanças no código ou nas funcionalidades. Verificação desta atualização: `git diff --check`; testes PHP e Lighthouse não foram repetidos por se tratar apenas de documentação.
- Próxima etapa pendente: **etapa 9 — testes, documentação e publicação**, ainda não iniciada. Domínio, hospedagem, configuração de produção, conteúdo pessoal confirmado, robots/sitemap e preview público continuam pendentes conforme o contexto.
- A pedido do proprietário, esta atualização é reunida no commit `fix: corrigido o status da etapa 8 na documentação`; consultar `git log` para o hash. Sem push.

## Histórico — retomada da validação da etapa 8

Lidos `AGENTS.md`, `PROJECT_CONTEXT.md`, este registro e `README.md`; código MVC e testes inspecionados antes de modificar arquivos. O repositório iniciou limpo, na branch `main`, commit **`469a890` — `Melhora responsividade, acessibilidade e SEO da etapa 8`**. A próxima etapa pendente continua sendo a **etapa 8**, cujo aceite estava incompleto; a etapa 9 não foi iniciada. A pedido do proprietário, esta entrega é reunida no commit `fix: corrigida a ausência do favicon no portfólio`; consultar `git log` para o hash. Sem push.

### Alterações e decisões técnicas

- A auditoria inicial da home identificou uma requisição automática a `http://localhost/favicon.ico`, com resposta 404 e erro de console. As notas eram 100 em desempenho/acessibilidade/SEO e 96 em boas práticas.
- Criado `public/assets/images/favicon.svg`, com o símbolo de código e a paleta da marca existente, sem fonte externa, conteúdo pessoal ou dependência adicional. O Controller base prepara `$faviconUrl` com o caminho-base; o layout compartilhado emite um único `link rel="icon"`, com atributo escapado. Isso elimina a busca pelo favicon ausente sem alterar Apache, rotas ou Models.
- `tests/run.php` verifica o favicon nos cenários de layout existentes, incluindo raiz, `/portifolio`, subdiretório aninhado e página 404. A lista de destinos permitidos foi atualizada para o novo asset.
- `README.md` documenta resultados, reprodução opcional do Lighthouse e limites de validação. `PROJECT_CONTEXT.md` mantém a etapa 8 desmarcada, agora indicando apenas o aceite manual de zoom/teclado pendente.
- Preservados CSS, arquitetura MVC, funcionalidades e dados reais: projetos/habilidades continuam vazios; contatos permanecem nulos. Nenhuma informação profissional foi inferida. Sem bibliotecas de execução novas, JavaScript ou mudanças em dependências Composer, permissões e configuração do servidor.

### Ambiente e método de auditoria

A ferramenta de interação retornou novamente **`apps: []`, `browsers: []`**. Foram encontrados Chrome local e Node fora do PATH: **Chrome headless 155**, **Node 24.19.0**. O **Lighthouse 13.5.0** foi instalado pelo registro npm apenas em `/tmp/portfolio-audit`, usando pnpm e store temporária, sem scripts de instalação. Essa auditoria CLI não conecta um navegador à ferramenta de interação nem permite declarar teclado/zoom manuais validados. Referência: [Lighthouse CLI oficial](https://github.com/GoogleChrome/lighthouse#using-the-node-cli).

Auditorias sequenciais, com categorias `performance,accessibility,best-practices,seo`, relatórios JSON/HTML e capturas fornecidas pelo Lighthouse. Home real mobile usou o preset padrão (412 CSS px); desktop usou 1280 CSS px. Os demais cenários usaram larguras explícitas, altura 900 e device scale factor 1; 768/1280 usaram preset desktop e 320/375, mobile. Throttling simulado dos presets foi mantido. As notas são de execuções locais de laboratório; não representam dados de campo/Core Web Vitals ou certificação de acessibilidade.

Catálogo preenchido, detalhes, habilidades e contato foram auditados em cópia isolada de `app/`, `public/`, `config/` e autoload Composer, servida em `127.0.0.1:8018` com raiz `public/`. Fixtures incluíram título/descrição/tecnologia/categoria sem espaços, e-mail longo, imagem horizontal, imagem sintética vertical 300×900 e projeto sem opcionais. A origem configurada foi o próprio servidor local de teste. Nada foi cadastrado na aplicação real; perfis externos e clientes de e-mail não foram acionados.

### Testes e evidências

| Verificação | Resultado |
|---|---|
| Suíte anterior com `E_ALL`, antes das mudanças | **967/967** aprovadas. |
| `php -d error_reporting=E_ALL tests/run.php`, após correção | **979/979** aprovadas. |
| Sintaxe de `app/`, `public/`, `config/`, `tests/` e `index.php` | **27 arquivos PHP**, sem erros. |
| `git diff --check` | Aprovado. |
| Apache: home e catálogo | **200**, HTML executado e favicon declarado uma única vez. |
| Apache: rota desconhecida | **404**, mantendo `noindex, follow` e layout compartilhado. |
| Apache: CSS, favicon SVG e preview PNG | **200**, MIME correto e bytes idênticos aos arquivos locais; SVG parseado sem erros. |
| Apache: configuração e layout privados | **403**. |

Notas Lighthouse após a correção, na ordem desempenho/acessibilidade/boas práticas/SEO:

| Cenário | Largura CSS | Notas |
|---|---|---|
| Home real mobile | 412 px | **100 / 100 / 100 / 100** |
| Home real desktop | 1280 px | **100 / 100 / 100 / 100** |
| Catálogo real vazio | 375 px | **100 / 100 / 100 / 100** |
| Home preenchida, fixture | 320 e 768 px | **100 / 100 / 100 / 100** em ambos |
| Catálogo preenchido, fixture | 375 e 1280 px | **100 / 100 / 100 / 100** em ambos |
| Detalhe horizontal e textos longos, fixture | 375 e 1280 px | **100 / 100 / 100 / 100** em ambos |
| Detalhe vertical, fixture | 320 px | **100 / 100 / 100 / 100** |
| Detalhe sem opcionais, fixture | 768 px | **100 / 100 / 100 / 100** |

Nas 11 execuções acima: FCP entre **0,2 e 0,8 s**, LCP entre **0,2 e 1,2 s**, TBT entre **0 e 10 ms** e CLS **0**. Sem erros de console ou avisos de execução; imagens, CSS e favicon carregaram com 200. O Lighthouse também consultou `robots.txt`, que permanece 404 conforme a pendência de domínio; esse request da auditoria não foi tratado como falha de asset da página.

O 404 real foi auditado separadamente em **320 e 1280 px**, apenas na categoria acessibilidade, com **100 em ambos**. Usado `--ignore-status-code` para permitir a inspeção sem alterar a resposta HTTP: os relatórios registram o aviso esperado de status 404. As duas capturas foram inspecionadas, com heading, retorno ao início e navegação dentro da largura disponível; não se atribuiu nota de SEO a uma página intencionalmente não indexável. Artefatos: `404-{320,1280}.report.{json,html}` e capturas correspondentes.

Capturas inspecionadas de home, catálogo e detalhes mostram quebra de textos longos, adaptação das colunas e tratamento de imagens nas quatro larguras previstas. Os retângulos dos nós registrados pelo Lighthouse ficaram dentro da largura configurada; isso é evidência parcial, não uma medição de `scrollWidth` de todos os elementos nem teste real de rolagem. Na captura expandida da home a 320 px, a imagem horizontal de um card com lazy loading não apareceu, embora a rede registre 200 e a mesma imagem esteja visível no catálogo/detalhe e na home a 768 px; conferir seu carregamento por rolagem real no aceite manual.

Relatórios e capturas desta sessão ficam em **`/tmp/portfolio-audit/`**, incluindo `summary.json`, `home-mobile-after.report.{json,html}` e os relatórios `fixture-*`. São artefatos temporários, podem desaparecer e não foram adicionados ao repositório. Comandos reproduzíveis estão no README. A execução de rede local/Chrome e instalação temporária exigiu escalada do sandbox; as proteções do Chrome foram mantidas.

Servidor temporário encerrado e cópia com fixtures removida após as auditorias. Nenhum aviso/erro de aplicação foi observado nas respostas e no log do servidor temporário. Composer não foi reinstalado nem validado novamente nesta retomada; seu autoload existente foi usado na integração.

### Aceite e pendências para retomar

- **Lighthouse mobile/desktop concluído e erro de console corrigido.** Nenhum outro problema de implementação foi identificado pelas auditorias que justificasse ampliar as alterações desta entrega.
- **Etapa 8 continua com aceite parcial:** falta interação manual em navegador conectado. Testar Tab/Shift+Tab/Enter, foco visível, salto ao conteúdo, âncoras e retorno de catálogo/detalhe/404; zoom real 200%/400%, rolagem horizontal e carregamento dos cards ao rolar. Não interpretar as notas 100 ou as capturas como substitutos desses testes.
- Confirmar domínio e conteúdo pessoal antes da publicação. Robots/sitemap, preview público no LinkedIn, imagens/projetos reais, contatos e currículo permanecem pendentes conforme o contexto; nenhum valor foi inventado.
- A próxima retomada deve terminar o aceite manual da **etapa 8**, sem começar a etapa 9 automaticamente. Não reinstalar o Composer ou alterar Apache/permissões para contornar a falta de navegador conectado.

## Histórico — implementação inicial da etapa 8: responsividade, acessibilidade e SEO

A pedido do proprietário, as alterações da etapa 7 foram reunidas no commit **`f864f44` — `Implementa contato e redes profissionais da etapa 7`**, sem push. Em seguida foi implementada exclusivamente a etapa 8. Árvore limpa após esse commit e antes destas mudanças; branch observada: `main`. A pedido do proprietário, as alterações da etapa 8 são reunidas no commit `Melhora responsividade, acessibilidade e SEO da etapa 8`; consultar `git log` para o hash. Sem push. O aceite visual/Lighthouse continua pendente. **Etapa 9 não iniciada.**

### Alterações realizadas

| Arquivo ou área | Alteração desta etapa |
|---|---|
| `config/app.php` | Acrescenta origem pública opcional via `APP_ORIGIN`, sem inventar domínio ou usar cabeçalhos da requisição. |
| `app/Core/Controller.php` | Prepara metadados compartilhados, normaliza descrição, valida origem e compõe URLs absolutas de canonical/compartilhamento a partir de caminhos conhecidos. |
| `app/Controllers/{Home,Project,Error}Controller.php` | Títulos/descrições por página, caminhos canônicos e dados de imagem do detalhe; 404 sinalizado como não indexável. |
| `app/Views/layouts/main.php` | Meta description, Open Graph, canonical opcional e noindex no erro; valores dinâmicos escapados. |
| `app/Models/Project.php` | Obtém dimensões reais das imagens; arquivo ilegível recebe fallback. Não muda o esquema de cadastro. |
| `app/Views/partials/project-list.php` e `projects/show.php` | Dimensões intrínsecas nos atributos da imagem; preserva recorte dos cards e lazy loading existente. |
| `app/Views/partials/header.php`, `project-list.php` e `project-technologies.php` | Preserva semântica de listas sem marcadores com `role="list"`. |
| `public/assets/css/style.css` | Navegação móvel em linha própria, colunas com mínimo zero, links dos cards com altura mínima e melhor quebra de textos longos; limita largura do link de salto. |
| `public/assets/images/social-preview.{svg,png}` (novos) | Fonte editável e PNG 1200×630 para compartilhamento; apenas título profissional confirmado e tema do site. |
| `tests/run.php` | SEO, imagens, origem válida/inválida, escaping, canonical, 404 noindex, contrastes e invariantes de navegação/semântica. |
| `README.md` e `PROJECT_CONTEXT.md` | Configuração, escopo, regeneração da imagem, evidências e pendências de aceite. |

### Decisões técnicas e limites de escopo

- Arquitetura preservada: Controllers escolhem os textos e caminhos de cada página; Core prepara URLs/metadados reutilizáveis; layout apenas os apresenta com escaping. Router, rotas e funcionalidades das etapas anteriores foram mantidos. Sem frameworks, novas dependências de execução, JavaScript, banco de dados ou alterações no Apache/permissões.
- Home recebe título com o posicionamento profissional confirmado; catálogo tem descrição própria; detalhe usa título/resumo do projeto. Descrições são texto, com espaços normalizados e atributos escapados. Sem biografia ou resultados inventados.
- `APP_ORIGIN` deve ser HTTP(S), com host e porta opcional, sem caminho (exceto `/` final), query, fragmento ou credenciais. Caminho-base permanece em `APP_BASE_PATH`. Origem ausente/inválida omite canonical, `og:url` e `og:image`, mantendo metadados textuais. Nenhuma URL é derivada do Host ou do esquema da requisição.
- Canonical utiliza o caminho conhecido da rota, incluindo slug validado/codificado. Query strings, barra final e variações de codificação não alteram sua identidade. 404 mantém status, descrição própria e `noindex, follow`; não recebe canonical ou metadados Open Graph de página válida.
- Open Graph inclui título, descrição, `website`, nome do site e `pt_BR`. Com origem válida, inclui URL e imagem absolutas, dimensões, MIME e alt. Detalhes usam sua imagem; ausência usa PNG padrão. O preview completo em LinkedIn e a geração de `robots.txt`/`sitemap.xml` dependem do domínio real, ainda pendente no contexto.
- `Project` fornece `image_width`/`image_height` automaticamente, sem novos campos a preencher. `getimagesize` lê somente arquivos locais já normalizados; imagem ilegível é omitida no detalhe e usa fallback nos cards. Não foram criados uploads ou otimizações de arquivos inexistentes. A revisão das imagens reais continua pendente de conteúdo.
- Ajustes de CSS tratam causas identificadas por inspeção: mínimos automáticos das colunas de grid, ausência de largura própria da navegação móvel, quebra de textos e área dos títulos clicáveis. Mantidos foco visível, salto, ordem natural de Tab e seções com `tabindex="-1"`; não há tabindex positivo ou menu que exija JavaScript.
- A imagem padrão usa a paleta existente e texto genérico do portfólio; o SVG é a fonte editável e o PNG tem **25.149 bytes**. Gerada com ImageMagick já disponível e inspecionada visualmente como artefato local. ImageMagick não é dependência da aplicação. CSS atualizado: **9.905 bytes**. Sem fontes remotas ou scripts adicionais.

### Testes e evidências desta entrega

| Verificação | Resultado |
|---|---|
| Suíte anterior | **782/782** aprovadas antes das mudanças; mesmas verificações continuaram passando após as mudanças iniciais. |
| `php -d error_reporting=E_ALL tests/run.php` | **967/967** aprovadas após testes da etapa 8. |
| Sintaxe em `app`, `public`, `config`, `tests` e `index.php` | **27 arquivos PHP**, sem erros. |
| `git diff --check` | Aprovado. |
| Metadados em raiz, `/portifolio` e subdiretório aninhado | Descrições, Open Graph, canonical, URLs com origem/caminho-base, dimensões e escaping aprovados. |
| Origem ausente/inválida | Sem URLs absolutas inventadas; credenciais, esquemas inseguros, subcaminho, query e fragmento rejeitados. |
| Rota parametrizada com query/barra final/slug codificado | Canonical usa rota normalizada; HTTP e testes isolados aprovados. |
| 404 | Status 404 e `noindex, follow`; sem canonical/OG de página válida, inclusive com domínio configurado. |
| Imagens | Dimensões intrínsecas nos cards/detalhes, lazy loading dos cards e fallback para arquivo ilegível aprovados; PNG padrão válido 1200×630. |
| Contraste dos tokens | 12 pares verificados. Menor contraste de textos/botões **7,61:1** e de foco sobre fundos **12,28:1**. Paleta preservada. Isso não representa certificação integral de acessibilidade. |
| Apache real, sem domínio configurado | Home/catálogo **200**; erro e detalhe desconhecido **404**; descrições/OG textual presentes e URLs absolutas omitidas. |
| Assets e arquivos privados no Apache | CSS e PNG **200**, MIME correto e bytes idênticos aos locais; configuração/layout privados **403**. |
| Cópia temporária com origem de teste e raiz `public/` | Home/catálogo/detalhe **200**, desconhecido **404**; canonical/OG completos corretos, imagens **200** com `image/png`; imagem ilegível usa preview padrão. |
| HTML servido | Layout e h1 únicos, IDs/headings/âncoras válidos, imagens com alt/dimensões; sem scripts, formulários ou código PHP exposto. |

A integração utilizou o autoload Composer existente, sem alterações de dependências. As fixtures de projeto/imagem e a origem ilustrativa foram usadas apenas em testes/cópia temporária. Nenhum domínio ou projeto foi publicado. Servidor temporário encerrado, cópia removida e log sem avisos/erros de aplicação. Acesso HTTP/porta temporária exigiu escalada do sandbox, sem mudanças de configuração ou permissões.

Referências técnicas consultadas: [Open Graph](https://ogp.me/), [canonical do Google Search Central](https://developers.google.com/search/docs/crawling-indexing/consolidate-duplicate-urls), [WCAG — contraste](https://www.w3.org/WAI/WCAG22/Understanding/contrast-minimum.html), [WCAG — reflow](https://www.w3.org/WAI/WCAG22/Understanding/reflow.html) e [semântica de listas no MDN](https://developer.mozilla.org/en-US/docs/Web/CSS/Reference/Properties/list-style#accessibility).

### Aceite e próxima retomada

- **Implementação da etapa 8 disponível; aceite permanece parcial.** O checklist continua desmarcado porque a usabilidade mobile/desktop e os principais problemas visuais/Lighthouse exigidos pelo roadmap ainda precisam de verificação. Inspeção de código, contraste calculado e testes de HTML/HTTP não comprovam reflow/foco reais ou desempenho medido.
- **Limite de ferramentas confirmado:** inventário `apps: []`, `browsers: []`; tentativa de abrir aba retornou `Browser is not available: iab`. Lighthouse não está disponível no PATH. Não foram executados testes visuais mobile/desktop, zoom, teclado real, console ou Lighthouse. Apenas a imagem de compartilhamento foi inspecionada visualmente.
- **Retomar a validação da etapa 8:** revisar home, catálogo vazio/preenchido, detalhes com/sem imagem e 404 em 320, 375, 768 e 1280 CSS px; zoom 200%/400%, textos longos e proporções de imagem. Verificar rolagem horizontal, foco/Tab/Shift+Tab/Enter, salto/âncoras, assets e console; rodar Lighthouse mobile/desktop e corrigir problemas observados. Documentar resultados antes de marcar o aceite.
- **Conteúdo e SEO público:** obter domínio, nome, biografia, projetos, habilidades, contatos e currículo confirmados. Configurar origem/caminho-base de produção, preparar robots/sitemap após domínio e validar preview real no LinkedIn. Nenhum desses valores foi inferido.
- **Etapa 9 não iniciada:** publicação, configuração do servidor e revisão final de produção dependem de nova autorização e das pendências acima. Não iniciar outra implementação automaticamente.

## Histórico — etapa 7: contato e redes profissionais

Implementada exclusivamente a etapa 7, a pedido do proprietário. Implementação funcional das etapas 1 a 7 disponível; **etapa 8 não iniciada**. Árvore de trabalho limpa no início, no commit `b1307cb` (`Implementa habilidades e tecnologias da etapa 6`). A pedido do proprietário, esta entrega é reunida no commit `Implementa contato e redes profissionais da etapa 7`; consultar `git log` para o hash. Sem push.

### Alterações realizadas

| Arquivo ou área | Alteração desta etapa |
|---|---|
| `config/contact.php` (novo) | Campos opcionais `github_url`, `linkedin_url`, `email` e `resume`; valores reais mantidos como `null` até confirmação do proprietário. |
| `app/Models/Contact.php` (novo) | Leitura/normalização dos dados; validação de URLs HTTP(S), e-mail e caminho do PDF local existente. |
| `app/Controllers/HomeController.php` e `public/index.php` | Injeção do Model e envio dos dados à home, preservando projetos e habilidades. |
| `app/Views/partials/contact.php` (novo) e `home/index.php` | Seção de contato após habilidades, com links individuais, `mailto:`, currículo opcional e aviso explícito de dados pendentes. |
| `app/Views/partials/header.php` | Link de retorno à seção da home em todas as páginas, respeitando caminho-base. |
| `public/assets/documents/.gitkeep` (novo) | Diretório reservado para o currículo público, sem PDF real ou conteúdo inventado. |
| `public/assets/css/style.css` | Lista de contatos com espaçamento e quebra de endereços longos, usando o tema existente. |
| `tests/run.php` | Fixtures isoladas de contatos, valores parciais/inválidos, e-mails especiais, download, caminho-base e estado vazio; preserva testes anteriores. |
| `README.md` e `PROJECT_CONTEXT.md` | Formato dos dados, manutenção do currículo, validação, checklist e limitações de conteúdo. |

### Decisões técnicas e limites de escopo

- Configuração lida exclusivamente pelo Model; Controller consulta `Contact::all()` e passa os valores à View. Model aceita arquivo alternativo para fixtures, seguindo o padrão existente. Não foram alterados Router, Core, páginas de projetos ou dados de habilidades.
- Campos ausentes/nulos são tratados individualmente: nenhum link vazio, fictício ou desabilitado entra na navegação. Sem dados utilizáveis, a seção informa “Contatos a confirmar”. Contatos reais foram solicitados ao proprietário durante a implementação; como não foram fornecidos até esta entrega, a configuração permanece sem dados pessoais.
- URLs seguem a política existente dos projetos: completas HTTP(S), sem credenciais, com atributos escapados. Links externos permanecem na mesma aba; não há `target="_blank"` nem necessidade de acrescentar `rel="noopener"`. A validação de formato não verifica a existência remota de um perfil.
- E-mail é validado como endereço, sem prefixo `mailto:` ou parâmetros. O texto é escapado e caracteres especiais são codificados na URI, impedindo que `?`, `&` ou sequências de percentuais no endereço se tornem parâmetros/cabeçalhos. Não houve abertura de cliente de e-mail, envio de mensagens ou implementação de formulário.
- Currículo utiliza caminho relativo sob `assets/documents/`, extensão `.pdf`, nomes ASCII documentados e arquivo existente. O caminho real deve continuar dentro do diretório, rejeitando inclusive links simbólicos para fora dele. Download usa o atributo HTML `download` e o caminho-base; não há endpoint extra, upload ou lógica de arquivo no Router. O arquivo final deve ser fornecido/aprovado pelo proprietário; nenhum currículo foi criado para publicação.
- Seção `#contato` usa h2 associado e `tabindex="-1"`; links têm nomes visíveis descritivos, o e-mail mostra o destinatário e o download informa PDF. Menu retorna à seção a partir de catálogo, detalhe e 404. CSS preserva tokens e estilos de foco existentes.
- Sem dependências, JavaScript, banco de dados, mudanças no Apache/permissões ou antecipação da revisão geral de SEO/acessibilidade da etapa 8.

### Testes e evidências desta entrega

| Verificação | Resultado |
|---|---|
| Suíte anterior, antes das mudanças | **696/696** aprovadas. |
| `php -d error_reporting=E_ALL tests/run.php` | **782/782** aprovadas. |
| Sintaxe em `app`, `public`, `config`, `tests` e `index.php` | **27 arquivos PHP**, sem erros. |
| `git diff --check` | Aprovado. |
| Fixtures de contato | Campos nulos/omitidos, links parciais, currículo isolado, URLs inseguras, e-mails inválidos/especiais, escaping e estado vazio aprovados. |
| PDF opcional | Arquivo local disponível habilita download; ausência/remoção/caminho inválido o omite. Fixture PDF temporária removida no final. |
| Raiz, `/portifolio` e subdiretório aninhado | Links, `mailto:`, download, IDs e headings verificados na renderização isolada. |
| Apache real | GET home/catálogo **200**; rota desconhecida e detalhe inexistente **404**. Seção/aviso presentes, menu retorna à home, sem dados inventados. |
| Apache: novos arquivos privados | `config/contact.php` e `app/Views/partials/contact.php` **403**. |
| Cópia temporária + servidor PHP com raiz `public/` | Home preenchida, catálogo e detalhe de fixture **200**, rota desconhecida **404**. Links de contato, retorno à seção, contatos parciais e retorno ao estado vazio aprovados. |
| Download HTTP da fixture | **200**, `Content-Type: application/pdf` e bytes idênticos ao PDF de teste. Link com atributo `download` presente; sem PDF no site real. |
| HTML/CSS via HTTP | H1/IDs únicos, headings e âncoras existentes, nenhum formulário ou código PHP exposto; CSS **200** e idêntico ao arquivo atualizado nos dois servidores. |

Integração confirmou autoload Composer existente sem alterar dependências. Acesso local/abertura da porta temporária executados com escalada do sandbox; nenhuma alteração no Apache ou nas permissões. O servidor temporário foi encerrado, a cópia com fixtures foi removida e o log não registrou avisos/erros de aplicação. As fixtures não consultam perfis externos nem enviam e-mails.

### Aceite, pendências e próxima retomada

- **Implementação funcional da etapa 7 validada com dados isolados:** links individuais, `mailto:`, PDF opcional e estado vazio disponíveis, sem formulário. Checklist marca a implementação; os contatos reais ainda não estão publicados nem foram validados remotamente.
- **Conteúdo/aceite final pendente:** obter GitHub, LinkedIn e e-mail confirmados e o currículo PDF caso exista. Preencher `config/contact.php`, conferir os perfis, o destinatário no aplicativo de e-mail e o download/leitura do documento final. A ausência desses dados não foi substituída por informações inferidas do usuário do sistema, projetos ou referências visuais.
- **Validação manual pendente:** conferir mobile/desktop, zoom, endereço longo, console, foco e Tab/Enter no menu, redes, e-mail e currículo. Inventário desta sessão: `apps: []`, `browsers: []`; não foi possível inspecionar visualmente nem usar teclado real. Testes de HTML/HTTP não substituem essa revisão.
- Próxima etapa do roadmap: **etapa 8 — responsividade, acessibilidade e SEO**, somente após novo pedido. Não foi iniciada nesta entrega; preservar as pendências de conteúdo e a verificação dos contatos reais.

## Histórico — etapa 6: habilidades e tecnologias

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
