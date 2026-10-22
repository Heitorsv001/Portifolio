# Validação local do redesign — etapa 9

> Atualização posterior: o responsável forneceu um retrato, já incluído na home e validado em `validation/portrait/`. As capturas e métricas deste relatório correspondem ao estado anterior à imagem; não representam uma nova medição de desempenho com o retrato.

Execução em 21/10/2026, após o aceite das etapas 1–8 e dos ajustes de conteúdo pelo responsável. PHP MVC, rotas, Models, conteúdo profissional e configuração de contatos preservados. Não houve publicação, habilitação do projeto oculto, commit ou push. A validação local foi executada; o aceite final do responsável e a auditoria Lighthouse permanecem pendentes.

## Alteração realizada

Em `public/assets/js/hero-particles.js`, a permissão de movimento passou a ser atualizada nos eventos de mídia e redimensionamento, em vez de consultar `MediaQueryList.matches` a cada quadro. Na verificação anterior ao ajuste, mudar a preferência de movimento ocultava o canvas pelo CSS, mas seu buffer permanecia alocado até um resize. Após o ajuste, movimento reduzido e ponteiro impreciso interrompem o efeito e liberam o buffer durante a visita; remover a redução de movimento retoma o desenho.

`tests/particles.js` ganhou uma regressão que impede consultas repetidas às preferências no ciclo de animação. Cores e tamanho aprovados das partículas foram mantidos: roxo `#5b21b6` e raio dobrado no tema claro. Não foram necessárias mudanças adicionais de layout, texto ou SEO nesta etapa.

## Resultados executados

| Verificação | Resultado |
| --- | --- |
| Suíte PHP com `E_ALL`, em cópia temporária | 1.601/1.601 |
| Regressões JavaScript em V8 com DOM/Canvas simulados | 72 de tema + 29 de navegação + 38 de partículas: 139/139 |
| Sintaxe PHP | 30 arquivos sem erros |
| `git diff --check` | Aprovado |
| Matriz no Chrome | 480/480 verificações em 48 combinações |
| Interações no Chrome | 39/39 |
| Partículas com desenho real no Chrome | 6/6 |
| Chrome com `public/` como raiz | 8 casos aprovados |
| HTTP em `/portifolio` e na raiz pública | 20 GET e 2 POST com status esperado |
| Assets dos dois caminhos-base | 10 URLs com HTTP 200, MIME e bytes conferidos |
| Dados/configurações comparados por SHA-256 | Todos preservados nesta etapa |

Ambiente: Linux, PHP 8.4.26 e Chrome Headless 156.0.8078.47. Navegador instalado executado por CLI e protocolo DevTools, com perfis temporários separados do perfil pessoal. Sem instalação de dependências no projeto. Chrome e servidor PHP auxiliar restritos a localhost, encerrados ao término.

## Responsividade, acessibilidade e funcionamento

A matriz cobre home, catálogo, detalhe do Gerenciador e 404, nos temas claro/escuro e nas larguras 320, 360, 390, 768, 1024 e 1440 CSS px, com altura de 900px e DPR 1. Conferidos overflow horizontal, IDs únicos, um h1 por página, referências de headings, nomes dos links/botões no DOM e na árvore de acessibilidade do Chrome, dimensões dos botões, metadados e indexação de 404.

Não houve overflow nas combinações avaliadas. O menor contraste dos textos encontrados sobre suas superfícies CSS calculadas foi **6,38:1**. O cálculo considera cores e transparências dos ancestrais; não constitui certificação WCAG nem medição de cada pixel das partículas. Foco e botões também continuam cobertos pelos testes dos tokens nos dois temas.

Testados com eventos nativos de teclado via DevTools: skip link, alternância de tema, abertura do menu, Tab até os links, Escape com restauração de foco, âncora de experiência sem ocultar o destino pelo header e mudança de breakpoint sem foco em elemento escondido. Saída de foco e clique externo fecham o menu; o rodapé retorna ao topo. Conferidos persistência entre recarga e rotas, preferência do sistema, valor salvo inválido e armazenamento indisponível.

Texto ampliado a 200% sem overflow em 320, 390, 768 e 1440px. Isso é ampliação da fonte raiz; não foi apresentado como teste do zoom nativo do navegador. Sem JavaScript, navegação e conteúdo continuam disponíveis, com controles inativos ocultos, em 390 e 1440px.

Partículas verificadas com ponteiro fino e toque emulado: desativação no mobile/movimento reduzido/ponteiro impreciso, retomada após redução de movimento, pausa fora do hero, buffer limitado em 4K e conservação das cores e do tamanho aprovado. Observados **25 desenhos em 1,1 segundo**, dentro do limite de 30fps; isso é uma amostra, não uma garantia de fluidez para todos os equipamentos.

No console/rede da matriz final houve **zero erros inesperados** e 12 mensagens de 404 correspondentes às visitas intencionais à rota inexistente. E-mail de contato continua apontando para `heitorsvareiro@gmail.com`, com rótulo “E-mail de contato”; nenhum aplicativo de e-mail foi aberto e nenhuma mensagem foi enviada.

## HTTP, links e metadados

Home, catálogo e os quatro detalhes visíveis responderam 200. Projeto oculto, slug desconhecido, slug inválido, rota inexistente e POST não registrado responderam 404. Testados no Apache em `/portifolio` e em servidor PHP temporário com `public/` como raiz. Corpo HTML, idioma/charset, headings, fragmentos, assets e ausência de avisos PHP conferidos. Suíte mantém testes de canonical/OG com origem configurada, escaping e valores inválidos; a execução local sem domínio não anunciou canonical inventado.

GitHub pessoal, quatro repositórios e as duas demonstrações responderam **HTTP 200** nesta execução. O LinkedIn retornou **999**, bloqueando a consulta automatizada; seu endereço foi preservado. Status 200 comprova resposta da página, sem validar funcionalidades dos projetos externos.

## Amostras de desempenho local

Uma navegação por página, cache de rede desativado, 1440×900, DPR 1, sem limitação de CPU/rede e aproximadamente 1,1s de observação após o carregamento. Valores coletados pelas APIs de performance do Chrome:

| Página | FCP | LCP observado | CLS observado | Tarefas longas observadas |
| --- | --- | --- | --- | --- |
| Home | 108ms | 108ms | 0 | 0 |
| Catálogo | 88ms | 88ms | 0 | 0 |
| Detalhe | 84ms | 84ms | 0 | 0 |

As amostras usam servidor local e janela curta de observação. Não são resultados Lighthouse, métricas de campo ou previsão de desempenho em hospedagem/celulares. Node.js/Lighthouse e Composer não estavam disponíveis; nenhum resultado dessas ferramentas foi atribuído à execução.

## Capturas e evidências

Capturas do próprio portfólio; não são capturas das aplicações dos projetos. Home antes/depois em 390, 768 e 1440px, nos dois temas. Comparação de layout com o mesmo tamanho e sem ponteiro fino nas duas séries; as capturas específicas `hero-mouse-*` mostram o efeito com mouse. O efeito é aleatório, portanto diferenças entre pontos não indicam mudança de layout. Foram geradas 30 imagens, incluindo páginas internas, teclado e ausência de JavaScript. Amostras foram abertas para inspeção visual, sem substituir o aceite do responsável.

| Amostra | Escuro | Claro |
| --- | --- | --- |
| Home 390px antes | [PNG](validation/stage9/before/home-390-dark.png) | [PNG](validation/stage9/before/home-390-light.png) |
| Home 390px depois | [PNG](validation/stage9/after/home-390-dark.png) | [PNG](validation/stage9/after/home-390-light.png) |
| Home 768px depois | [PNG](validation/stage9/after/home-768-dark.png) | [PNG](validation/stage9/after/home-768-light.png) |
| Home 1440px depois | [PNG](validation/stage9/after/home-1440-dark.png) | [PNG](validation/stage9/after/home-1440-light.png) |
| Hero com mouse | [PNG](validation/stage9/after/hero-mouse-1440-dark.png) | [PNG](validation/stage9/after/hero-mouse-1440-light.png) |
| Catálogo 1440px | [PNG](validation/stage9/after/catalog-1440-dark.png) | [PNG](validation/stage9/after/catalog-1440-light.png) |
| Detalhe 390px | [PNG](validation/stage9/after/detail-390-dark.png) | [PNG](validation/stage9/after/detail-390-light.png) |
| Erro 390px | [PNG](validation/stage9/after/error-390-dark.png) | [PNG](validation/stage9/after/error-390-light.png) |

[Menu com foco](validation/stage9/after/keyboard-menu-390-dark.png), [skip link](validation/stage9/after/keyboard-skip-390-dark.png), [home sem JavaScript](validation/stage9/after/no-js-home-390.png) e [estado da série final de comparação](validation/stage9/screenshot-state.json).

Evidências estruturadas: [matriz](validation/stage9/browser-matrix.json), [interações](validation/stage9/browser-interactions.json), [eventos](validation/stage9/browser-events.json), [desempenho e partículas](validation/stage9/local-performance.json), [HTTP/assets/links](validation/stage9/http-validation.json), [raiz pública no Chrome](validation/stage9/root-browser.json), [configurações preservadas](validation/stage9/preserved-data.json) e [suítes](validation/stage9/test-results.json).

## Pendências

- Aceite visual final do responsável, incluindo seu navegador e dispositivos habituais.
- Lighthouse mobile/desktop e avaliação sob condições representativas de hospedagem e hardware; zoom nativo e uso com tecnologia assistiva não foram testados.
- Disponibilidade do LinkedIn em navegação manual, devido ao bloqueio da consulta automatizada.
- Foto autorizada, capturas reais das aplicações e PDF de currículo continuam opcionais e ausentes.
- Domínio, hospedagem e publicação continuam fora desta execução. O projeto do próprio portfólio permanece oculto.
