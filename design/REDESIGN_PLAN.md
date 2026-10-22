# Plano de redesign do portfólio PHP MVC

Data: 21/10/2026. Estado atual: etapas 1–8 confirmadas pelo responsável; etapa 9 executada e validada localmente. Aceite visual final e Lighthouse pendentes; resultados e limites em `REDESIGN_VALIDATION.md`.

Na execução inicial foi criado somente este documento. Etapas 1–9 executadas após autorização do responsável, com os limites de validação final explicitados no relatório. Cada etapa deve ter verificação funcional e revisão visual. A numeração é a do redesign, não a do roadmap original do MVP. Não inclui publicação, `git add`, commit ou push.

Atualização após a etapa 9: corrigida a atualização das preferências das partículas durante a visita, evitando consultas de mídia a cada quadro. 1.601/1.601 PHP com `E_ALL`, 139/139 JavaScript em V8, sintaxe de 30 arquivos PHP e HTTP nos dois caminhos-base aprovados. Chrome instalado usado com perfis temporários: 480/480 verificações de matriz, 39/39 de interação e 6/6 de partículas, mais oito casos na raiz pública. Geradas 30 capturas; nenhuma alteração de dados ou conteúdo profissional nesta etapa. E-mail autorizado anteriormente preservado com rótulo “E-mail de contato”. Metadados, headings, foco, temas, reflow de texto, contraste calculado e console/rede conferidos. LinkedIn bloqueou a consulta automatizada; endereço preservado. Lighthouse, zoom nativo, tecnologias assistivas e aceite visual do responsável ainda pendentes. Não publicar ou habilitar o projeto oculto automaticamente. Os registros seguintes descrevem o estado ao final das etapas anteriores.

Atualização após a etapa 8: `social-links.php` reutilizado no rodapé de todas as páginas, com nome, título e retorno ao topo. Contatos normalizados preparados pelo Controller base somente quando a página não os informa, sem substituir fixtures da home. Contato com grid fluido e áreas de clique ampliadas; e-mail/PDF condicionais preservados e não publicados. Dados e Models intactos. Validações: 1.602/1.602 PHP com `E_ALL`, 138/138 JavaScript em V8 com simulação, sintaxe de 30 arquivos PHP e HTTP Apache aprovados, incluindo catálogo, quatro detalhes e três casos de 404. Etapa 7 e ajustes editoriais de Sobre confirmados pelo responsável; revisão visual da etapa 8 pendente. Não avançar à etapa 9 automaticamente.

Atualização após a etapa 7: oito categorias e 29 itens de habilidades preservados, com SVGs locais decorativos por categoria e nomes legíveis mesmo sem os ícones. Formação movida para `#formacao` após habilidades e antes de contato, preservando curso, IFMS, período com previsão, situação em andamento e quatro nomes de certificações. Sem alteração dos dados ou inclusão de metadados de emissão. Validações: 1.362/1.362 PHP com `E_ALL`, 138/138 JavaScript de regressão em V8 com simulação, sintaxe de 30 arquivos PHP e HTTP Apache aprovados. Etapa 6 confirmada pelo responsável; revisão visual da etapa 7 pendente. Não avançar à etapa 8 automaticamente. Os registros abaixo descrevem o estado ao final de cada etapa anterior.

Atualização após a etapa 6: home com dois destaques amplos e dois secundários compactos, sem duplicação; um partial de listagem com três apresentações, incluindo catálogo. Dados e Model preservados, portfólio oculto, detalhes integrais com retorno principal à home e catálogo disponível. Validações: 1.326/1.326 PHP, 138/138 JavaScript de regressão em V8 com simulação, sintaxe de 30 arquivos PHP e HTTP Apache aprovados; demos cadastradas de Festividades/Notícias responderam 200. Etapa 5 confirmada pelo responsável; revisão visual da etapa 6 pendente. Não avançar à etapa 7 automaticamente.

Atualização após a etapa 5: experiência é a primeira seção após o hero, seguida de Sobre. Período em coluna no desktop e acima no mobile, cargo/instituição/projeto/descrição preservados e tecnologias existentes organizadas em tags no perfil. Objetivos incorporados a Sobre com âncoras preservadas; formação e certificações continuam disponíveis. Valores profissionais anteriores conferidos integralmente contra cópia prévia. Validações: 1.297/1.297 PHP, 72/72 de tema, 29/29 de navegação e 37/37 de partículas; sintaxe de 30 arquivos PHP e HTTP Apache aprovados. Aceite das partículas confirmado pelo responsável; revisão visual da etapa 5 pendente. Não avançar à etapa 6 automaticamente.

Atualização após a etapa 4: Canvas 2D decorativo na home, até 64 pontos, desenho limitado a 30fps, DPR até 2 e buffer próximo de 6 milhões de pixels. Movimento lento, reação suave ao mouse, cores dos tokens e pausa fora da viewport/aba oculta. Desligado no mobile, ponteiro impreciso e movimento reduzido. Validações: 1.251/1.251 PHP, 72/72 de tema, 29/29 de navegação e 35/35 de partículas em V8 com DOM/Canvas simulados; sintaxe de 30 arquivos PHP e HTTP Apache aprovados. Hero confirmado pelo responsável; revisão visual e desempenho gráfico real das partículas pendentes. Não avançar à etapa 5 automaticamente.

Atualização após a etapa 3: hero central com avatar neutro até receber foto autorizada, nome, subtítulo exato e ícones sociais. Título profissional, condição acadêmica e introdução realocados para Sobre, sem mudanças de valores. Partial social usa os contatos já fornecidos pelo HomeController; a preparação de contatos para rodapés de páginas internas foi mantida para a etapa 8. Validações: 1.232/1.232 PHP, 72/72 de tema, 29/29 de navegação em V8 com DOM simulado, sintaxe de 30 arquivos PHP e HTTP Apache aprovados. Revisão visual da etapa 2 confirmada pelo responsável; revisão da etapa 3 pendente. Não avançar às partículas automaticamente.

Atualização após a etapa 2: header transparente no desktop da home, superfície ao rolar, menu compacto abaixo de 75rem e âncoras funcionais em todas as páginas. IDs de experiência/formação foram aplicados aos títulos atuais, sem reorganizar conteúdo antes das etapas 5 e 7. Etapa 1 confirmada pelo responsável antes desta autorização. Validações da etapa 2: 1.181/1.181 PHP, 72/72 de tema, 29/29 de navegação em V8 com DOM/eventos simulados, sintaxe de 29 arquivos PHP e integração Apache aprovados. Revisão visual e teclado real da etapa 2 continuam pendentes por indisponibilidade de navegador. Não avançar à etapa 3 automaticamente.

Atualização após a etapa 1: tokens escuro/claro e controle de tema compartilhado implementados, preservando dados e MVC. Validações: 1.073/1.073 verificações PHP, 59/59 verificações JavaScript em V8 com DOM simulado, 29 arquivos PHP sem erros de sintaxe e integração HTTP no servidor PHP local. Sem navegador conectado, revisão visual mobile/desktop, teclado real e flash continuam pendentes. Fontes locais agora disponíveis em `design/DESIGN_GUIDE.md` e `design/Imagem do ChatGPT 21 de out. de 2026, 18_12_30.png`; os caminhos da seção 1 registram o diagnóstico inicial. Detalhes em `../PROGRESS.md`.

## 1. Fontes consultadas e precedência

| Referência solicitada | Arquivo efetivamente encontrado e analisado |
| --- | --- |
| `AGENTS.md` | `../AGENTS.md`, além das preferências fornecidas na sessão |
| `PROJECT_CONTEXT.md` | `../PROJECT_CONTEXT.md` |
| `PORTFOLIO_CONTENT_GUIDE.md` | `../PORTFOLIO_CONTENT_GUIDE_ATUALIZADO.md`; o nome sem sufixo não existe |
| `design/DESIGN_GUIDE.md` | `../config/design/DESIGN_GUIDE.md` |
| `design/references/hero-reference.png` | `../config/design/Imagem do ChatGPT 21 de out. de 2026, 18_12_30.png`; o caminho solicitado não existe |

A imagem disponível foi aberta e corresponde à captura de diogotc.com descrita no guia: apresentação central com retrato circular e fundo de pontos conectados. Ela foi adotada como referência equivalente, sem mover, renomear ou copiar o original. Também foram consultados README, registros de PROGRESS, código, configurações, testes e a estrutura atual de [brittanychiang.com](https://brittanychiang.com/).

As decisões explícitas do DESIGN_GUIDE prevalecem sobre propostas visuais antigas do guia de conteúdo e do contexto: subtítulo exato, ausência de CTA no hero, experiência primeiro e alternância de tema entram neste redesign. A referência anterior ao iuricode não determina a nova composição. Os fatos profissionais e dados atuais permanecem preservados.

Há registros históricos divergentes: o contexto menciona status uniformes e apenas uma demo; os arquivos atuais e o README já registram Crowns descontinuado e demos de Festividades e Notícias. O plano preserva `config/projects.php` como está, sem restaurar estados antigos. Gerenciador e Crowns continuam destacados; a cautela sobre autenticação descrita nos guias não autoriza remover o destaque nem declarar o projeto pronto para produção.

## 2. Diagnóstico do código existente

### Arquitetura e funcionalidades

- PHP `^8.2`, Composer com autoload PSR-4 `App\\` e nenhuma biblioteca externa de execução. Há `composer.lock` e suíte própria em `tests/run.php`, apesar do texto inicial desatualizado do AGENTS sobre ausência de dependências/testes.
- `public/index.php` registra GET `/`, `/projetos` e `/projetos/{slug}`; `index.php` mantém compatibilidade com a instalação Apache. Rotas, métodos e slugs desconhecidos retornam 404.
- `app/Core/Router.php` trata caminhos-base e parâmetros. `app/Core/Controller.php` prepara URLs, metadados, canonical e imagem social, extrai os dados e inclui o layout compartilhado.
- Models `Profile`, `Project`, `Skill` e `Contact` leem as configurações. `Project` filtra invisíveis, valida links e imagens e obtém dimensões. `Contact` valida redes, e-mail e PDF. Preservar essas regras.
- As Views escapam conteúdo dinâmico. Layout comum contém idioma, skip link, header, main, footer, favicon e SEO; o 404 recebe `noindex`.
- `APP_BASE_PATH` permite a raiz pública ou `/portifolio`; `APP_ORIGIN` habilita URLs absolutas somente quando válido. Não inferir domínio a partir da requisição.

### Interface atual e diferenças para o guia

| Área | Implementação atual | Mudança planejada |
| --- | --- | --- |
| Estilos | Um `public/assets/css/style.css`, tokens escuros/violeta, fonte do sistema, breakpoint em 48rem | Evoluir os tokens para dois temas, tipografia de metadados, espaçamentos e estados compartilhados |
| Scripts | Nenhum JavaScript de interface | JS próprio mínimo para tema, menu e partículas, com melhoria progressiva |
| Header | Superfície opaca, navegação em linhas flexíveis, sem hambúrguer | Transparência sobre hero, superfície legível ao rolar, menu mobile e controle de tema |
| Hero | Alinhado à esquerda, título profissional, condição de estudante, introdução e três botões | Retrato/fallback circular, nome central, subtítulo exato e somente ícones sociais |
| Conteúdo | Sobre reúne formação, experiência e certificados; objetivos têm seção própria | Experiência imediatamente após hero; Sobre com introdução e objetivos preservados; formação/certificados após habilidades |
| Projetos | Home recebe apenas `featured()`; catálogo mostra quatro cards com o mesmo partial | Home com dois destaques amplos e dois secundários compactos; catálogo continua disponível |
| Detalhes | Resumo, status, descrição completa, tecnologias, links e retorno ao catálogo | Mesmo conteúdo em composição concisa; retorno principal para `/#projetos` |
| Habilidades | Grupos de texto, sem ícones | Manter todas as categorias e itens; acrescentar ícones acompanhados de nomes |
| Rodapé | Nome, título e retorno ao topo, sem redes | Nome e redes discretas em todas as páginas, preservando os dados profissionais |

O `main` atual acumula `.site-main.container`, limitando toda a home e acrescentando padding superior/inferior. O hero em tela inteira exige separar a largura das seções da camada de fundo e revisar esse padding apenas na home. Catálogo, detalhe e 404 devem manter contêiner e espaçamento próprios.

### Conteúdo que deve permanecer

- Nome e título profissional atuais; formação em andamento no IFMS, janeiro/2024 a dezembro/2026 com previsão; estágio de março a outubro/2026, sem presumir continuidade.
- Todos os parágrafos de `about`, a introdução e os objetivos existentes. Retirar texto do hero significa realocá-lo, não apagá-lo ou reescrevê-lo. A âncora `#objetivos` continua válida dentro do bloco de Sobre/objetivos.
- Experiência no Arco Literário como estágio no IFMS, com atividades e tecnologias confirmadas; não criar card de projeto pessoal para ela.
- Quatro certificações somente pelos nomes, sem emissor, data ou verificação inventados.
- Todas as habilidades atuais, inclusive competências pessoais e ferramentas de IA; os grupos sugeridos no guia visual não justificam descartar categorias ou incluir novas competências.
- Cinco registros de projetos, quatro visíveis. Gerenciador de Permanências e Crowns são destaques; Festividades de Fim de Ano e Notícias são secundários. Portfólio MVC em PHP continua com `visible => false` e `featured => false`, inclusive 404 por acesso direto.
- Slugs, textos, status, tecnologias, repositórios, demos e limitações atuais. Todas as imagens de projeto são nulas; não reservar colunas vazias nem inventar screenshots.
- GitHub e LinkedIn aprovados; e-mail e currículo continuam nulos. Não copiar o endereço de e-mail contido no guia para a interface.

## 3. Análise das referências e direção visual

### Imagem local: composição do hero

A captura de 1920 × 1080 inclui barras do sistema e do navegador na parte superior; elas não pertencem ao site. Na área útil, há menu leve no topo, fundo quase preto e amplo espaço em torno do conjunto retrato → nome → subtítulo → redes. O nome é o elemento de maior peso; os pontos cinzentos e conexões finas ficam em segundo plano.

Aplicação: centralizar Heitor Silva Vareiro, usar círculo de aproximadamente 88–112px no desktop, nome com `clamp`, subtítulo exatamente **“Desenvolvedor Web e estudante”** e GitHub/LinkedIn abaixo. No mobile, círculo de 72–88px e hero mais compacto. Usar roxo apenas como acento. Não reproduzir nome, foto, plataformas, links, barras do navegador ou distribuição exata das partículas da captura.

A screenshot comprova a aparência estática, não o comportamento do movimento. A animação lenta e a reação discreta ao mouse são requisitos do DESIGN_GUIDE, não observações verificadas na imagem.

### Brittany Chiang: organização editorial

Na estrutura consultada, experiências articulam período, cargo, descrição e tecnologias; projetos combinam título, síntese, mídia e informações técnicas. Esses padrões orientam a leitura dos blocos abaixo do hero. [Fonte: portfólio de Brittany Chiang](https://brittanychiang.com/).

Aplicação proposta: datas discretas em monoespaçada, cargo/título em destaque, descrições com largura limitada e tecnologias em tags. No mobile, período acima do texto. Não importar biografia, tecnologias, projetos ou números da autora, nem reproduzir sua estrutura integral. A consulta desta execução foi estrutural, pelo HTML disponibilizado; não houve auditoria das interações ou do comportamento responsivo da referência.

### Organização final da home

1. Hero central, com retrato autorizado ou fallback tipográfico neutro, nome, subtítulo e redes.
2. `#experiencia`: estágio, período, instituição, Arco Literário, atividades e tecnologias confirmadas.
3. `#sobre`: biografia e introdução preservadas, com `#objetivos` e seus objetivos profissionais.
4. `#projetos`: destaques amplos e subseção compacta de outros projetos, sem repetição de itens.
5. `#habilidades`: categorias atuais, ícones e nomes.
6. `#formacao`: formação em andamento e lista discreta das quatro certificações.
7. `#contato` e rodapé: contatos existentes, nome, redes e retorno ao topo, evitando repetição excessiva de chamadas.

O menu principal aponta para Experiência, Sobre, Projetos, Habilidades, Formação e Contato. A rota `/projetos` permanece acessível por link na seção de projetos e por URL direta. Em catálogo, detalhe e 404, âncoras devem incluir `$homeUrl`; `#sobre` isolado nessas páginas seria incorreto.

## 4. Mapeamento de arquivos para a implementação futura

### Arquivos existentes a alterar

| Arquivo | Alteração e motivo | Etapas |
| --- | --- | --- |
| `public/assets/css/style.css` | Centralizar tokens dos dois temas, tipografia e espaçamento; estilos de controles, hero, seções, projetos e footer; responsividade e movimento reduzido | 1–9 |
| `app/Views/layouts/main.php` | Inicialização de tema antes do CSS; scripts próprios; distinguir layout da home para hero amplo; preservar main, skip link e todos os metadados | 1–4 |
| `app/Views/partials/header.php` | Âncoras, botão de tema e botão/painel mobile acessíveis; manter marca e navegação entre páginas | 1–2 |
| `app/Views/home/index.php` | Novo hero, realocação integral do conteúdo e seções na ordem planejada; destaques e secundários | 3–7 |
| `app/Controllers/HomeController.php` | Disponibilizar secundários a partir de `Project::all()` e flags existentes; compartilhar contatos normalizados com o layout; manter metadados profissionais | 3, 6, 8 |
| `app/Core/Controller.php` | Preparar contatos compartilhados para o layout de todas as páginas por dependência `Contact`, sem leitura de configuração nas Views; preservar URLs, SEO e assinaturas compatíveis | 3, 8 |
| `app/Views/partials/project-list.php` | Diferenciar apresentação ampla/compacta por parâmetro de apresentação, mantendo links, estados vazios, imagens opcionais e conteúdo comum | 6 |
| `app/Views/projects/index.php` | Harmonizar o catálogo com a home, preservando todos os projetos visíveis e a rota | 6 |
| `app/Views/projects/show.php` | Retorno para `$homeUrl . '#projetos'`; composição concisa sem perder descrição, ressalvas, status, tecnologias ou links | 6 |
| `app/Views/partials/skills.php` | Ícones com fallback textual, categorias e itens intactos; ajustar numeração de seção | 7 |
| `app/Views/partials/contact.php` | Integrar visualmente contato/rodapé e nova numeração; manter tratamento de e-mail/PDF e estados vazios | 8 |
| `app/Views/partials/footer.php` | Redes por partial compartilhado, nome e título existentes; retorno ao topo em todas as páginas | 8 |
| `tests/run.php` | Adequar contratos visuais intencionalmente alterados e verificar temas, ordem, navegação, secundários, redes em todas as páginas e preservação de dados | Conforme a etapa |
| `README.md`, `PROJECT_CONTEXT.md`, `PROGRESS.md` | Registrar decisões finais, scripts, verificações e limitações quando implementados; conservar histórico e separar redesign de deploy | Ao concluir etapas relevantes |

A dependência de contatos no Controller base deve ser opcional e manter a construção atual dos Controllers. O HomeController deve reaproveitar o `Contact` já injetado para que hero, contato e rodapé usem a mesma fonte, inclusive nas fixtures. Não colocar acesso a Model/configuração dentro de partials. Essa alteração serve apenas aos dados comuns de apresentação; Router e fluxo MVC permanecem iguais.

Para projetos, calcular coleções uma única vez no HomeController a partir dos projetos visíveis, separando os que têm `featured === true` dos demais. Não adicionar novo Model nem fazer essa seleção lendo `config/projects.php` nas Views. Reutilizar `project-technologies.php` e `project-links.php`; extrair um partial de item somente se a variação de apresentação produzir duplicação real.

### Arquivos novos previstos

| Arquivo | Finalidade | Etapa |
| --- | --- | --- |
| `public/assets/js/theme-init.js` | Script pequeno carregado antes do CSS para resolver tema salvo/sistema/padrão, tolerando falha de armazenamento | 1 |
| `public/assets/js/site.js` | Alternância manual e sincronização do controle de tema; depois, comportamento do menu e estado do header | 1–2 |
| `app/Views/partials/social-links.php` | Links de GitHub e LinkedIn com SVG local/inline, nomes acessíveis e contatos normalizados; reutilizado no hero e rodapé | 3, 8 |
| `public/assets/js/hero-particles.js` | Canvas 2D exclusivo do hero, com limites e pausas de animação | 4 |

Não criar automaticamente um arquivo para cada componente conceitual do guia. Hero, experiência e formação podem permanecer na View da home enquanto não houver reutilização. Não há necessidade de framework, biblioteca de partículas, pacote de ícones ou fonte remota.

### Alterações condicionais e arquivos preservados

- `config/profile.php`: se necessário, acrescentar somente campos de apresentação como `hero_subtitle` e lista das tecnologias já confirmadas do estágio. Manter todas as chaves e valores profissionais existentes. Evitar extrair tags por análise textual da descrição. O título `headline` continua representando o posicionamento profissional, independentemente do subtítulo visual do hero.
- Foto pessoal: arquivo futuro em `public/assets/images/`, somente após fornecimento/autorização. Até lá, círculo tipográfico neutro, sem requisição quebrada. Uma futura integração deve validar caminho e dimensões antes de renderizar.
- Capturas reais: opcionais em `public/assets/images/projects/`; somente quando fornecidas e aprovadas. Usar a validação existente de `Project` e atualizar apenas `image` quando isso for autorizado. O redesign funciona sem elas.
- Ícones de habilidades: SVGs locais selecionados, se necessários; nomes continuam legíveis mesmo sem ícones. Não acrescentar JavaFX ou qualquer outra competência apenas porque aparece entre as sugestões do guia visual.
- `app/Views/errors/404.php`: a identidade deve acompanhar o CSS/layout; editar a View somente se a validação revelar necessidade concreta. Manter mensagem, retorno e status.
- Preservar `app/Core/Router.php`, Models existentes, `ProjectController.php`, `ErrorController.php`, front controllers, `config/app.php`, `config/projects.php`, `config/skills.php`, `config/contact.php`, Composer, `.htaccess`, permissões e configuração Apache. Não há mudança de regras de negócio prevista.
- Favicon e previews sociais atuais permanecem; eventual harmonização visual exige necessidade demonstrada e não inclui trocar o título profissional pelo subtítulo do hero.
- Guias e referência em `config/design/` ficam na localização atual. Esta execução não reorganiza documentação nem ativos fornecidos.

## 5. Etapas de implementação e critérios de aceite

### Etapa 0 — Diagnóstico e plano (esta entrega)

Entrega: inventário, análise das referências, arquivos previstos, dependências e matriz de validação neste documento.

Aceite: somente `design/REDESIGN_PLAN.md` é criado; código e conteúdo permanecem intactos; verificações efetivamente executadas ficam registradas na seção 8.

### Etapa 1 — Tokens e temas

Evoluir os tokens existentes, preservando nomes úteis como `--color-background` em vez de renomear todo o CSS para `--color-bg`. Adotar a paleta proposta no DESIGN_GUIDE como ponto inicial, incluindo variantes de hover, texto sobre acento e foco para ambos os temas. Fonte principal do sistema e `ui-monospace` para metadados evitam novos downloads.

Resolver tema nesta ordem: valor salvo válido (`dark`/`light`), preferência do sistema, escuro. Script de inicialização pequeno e externo antes da folha de estilos; demais interações com `defer`. Tratar falha de `localStorage`/`matchMedia` e alterações de preferência do sistema quando não houver escolha manual. Sem JS, a página continua legível com tema CSS padrão e controle inoperante oculto.

Aceite: todas as páginas nos dois temas; preferência persiste entre rotas e recargas; valor salvo inválido não quebra carregamento; bloqueio do armazenamento não impede uso; sem flash evidente no teste de recarga; controle anuncia o tema de destino; contraste AA e foco verificados. Confirmar compatibilidade com eventual CSP do servidor antes de escolher a integração final.

### Etapa 2 — Header e navegação

Menu transparente sobre o hero desktop, superfície suave ao rolar e fundo legível nas páginas internas. Usar painel mobile não modal como primeira opção, evitando bloqueio de rolagem e armadilha de foco desnecessários. JS fecha ao escolher destino, pressionar Escape, sair apropriadamente com o foco ou retornar ao breakpoint desktop. Escape devolve foco ao acionador. `aria-expanded`, `aria-controls` e rótulos acompanham o estado. Sem JS, links permanecem expostos e utilizáveis.

Aceite: todas as âncoras funcionam na home e a partir de outras páginas; `scroll-margin-top` protege títulos; Tab/Shift+Tab, Enter, Espaço e Escape funcionam; itens recolhidos não recebem foco; alvos de toque de pelo menos 44 × 44px; sem colisão entre nome, seis destinos e controles em 1024px. Antecipar o menu compacto se o conteúdo não couber.

### Etapa 3 — Hero

Centralizar círculo, nome, subtítulo exato e ícones sociais; desktop com `min-height: 100svh` e fallback, considerando a sobreposição do header. Mobile baseado no conteúdo, com aproximadamente `75svh` apenas como ponto inicial. Realocar introdução e condição acadêmica para Sobre, mantendo os textos. Adicionar o partial social e seus dados compartilhados sem duplicar URLs.

Aceite: composição central com respiro; nome legível e sem cortes; IFMS fora do subtítulo e presente no conteúdo; nenhum CTA no hero; foto somente se autorizada, caso contrário fallback neutro. Redes com nomes acessíveis e foco visível. Catálogo, detalhe e 404 não herdam a altura de tela inteira.

### Etapa 4 — Partículas

Canvas decorativo atrás do hero, `aria-hidden="true"` e `pointer-events: none`. Observar o ponteiro pela área do hero, sem capturar cliques. Limitar quantidade, distância de conexão e pixel ratio (ponto inicial: máximo 2); usar `requestAnimationFrame` e pausar quando documento estiver oculto ou hero fora da viewport. Evitar múltiplos loops ao retomar. Atualizar cores ao trocar o tema.

Aceite: movimento lento, reação discreta, texto sempre legível; links, seleção e rolagem intactos; em telas móveis/ponteiro sem precisão, desligar ou reduzir drasticamente; com movimento reduzido, fundo estático e nenhum loop ativo. Falha do script não oculta conteúdo. Comparar desempenho com partículas desligadas e ligadas, incluindo inspeção de tarefas longas.

### Etapa 5 — Experiência e Sobre

Extrair experiência de dentro de Sobre para a primeira seção após o hero. Organizar período, cargo, instituição, projeto, atividades e tecnologias confirmadas; no mobile, metadados acima do texto. Manter biografia, introdução, condição de estudante e objetivos. Formação e certificados continuam disponíveis durante a transição até a etapa 7, sem remoção antecipada.

Aceite: ordem hero → experiência → Sobre; dados e parágrafos anteriores preservados; nenhuma métrica ou responsabilidade nova; estágio e formação sem alegação de continuidade/conclusão. `#objetivos` e âncoras antigas continuam resolvendo.

### Etapa 6 — Projetos e páginas internas

Disponibilizar destaques e secundários na home usando os mesmos dados normalizados do catálogo. Destaques amplos, com mídia lateral apenas quando real; sem imagem, texto ocupa o espaço. Secundários em lista compacta com título, síntese, status, tecnologias e links. Preservar detalhes completos e ressalvas em layout conciso; não apagar descrição para encurtar a página.

Aceite: Gerenciador e Crowns aparecem uma vez nos destaques; Festividades e Notícias uma vez nos secundários; catálogo mantém quatro itens e os quatro detalhes funcionam. Projeto oculto, desconhecido ou inválido retorna 404. As duas demos cadastradas permanecem; não presumir disponibilidade sem verificar. Retorno principal do detalhe alcança a seção de projetos na home. Imagens/demos ausentes não produzem link ou área quebrada.

### Etapa 7 — Habilidades, formação e certificações

Preservar categorias e conhecimentos atuais, acrescentar ícones sempre com nomes e fallback. Mover formação/certificados para `#formacao` após habilidades. Certificações como lista discreta, sem novas informações de emissão.

Aceite: todas as habilidades atuais continuam presentes; sem percentuais ou níveis inventados; quatro nomes de certificados corretos; formação em andamento, período com previsão; IFMS aparece em Sobre, Experiência e Formação. Nenhuma nova biografia é criada.

### Etapa 8 — Contato e rodapé

Reutilizar `social-links.php` no rodapé de home, catálogo, detalhes e 404. Manter nome e título profissional em composição discreta. Harmonizar a seção de contato sem remover os caminhos condicionais de e-mail e currículo existentes.

Aceite: redes disponíveis e corretamente escapadas em todas as páginas, inclusive erro por slug; ausência de contatos não gera links vazios ou avisos PHP. Fixtures de contato injetadas na home também alimentam hero/rodapé. Sem divulgação de e-mail ou PDF; retorno ao topo funcional.

### Etapa 9 — Polimento e validação final

Revisar os critérios da seção 6, metadados, teclado, zoom, console, carregamento, movimento e armazenamento. Atualizar documentação com resultados reais, diferenças visuais e pendências; produzir capturas do próprio projeto. Não publicar ou habilitar o card do portfólio como parte desta etapa.

Aceite: validações funcionais aprovadas e evidências visuais em ambos os temas; pendências explicitamente registradas, sem apresentar notas ou testes históricos como resultados atuais. Nenhuma alteração de conteúdo profissional, regra de dados, dependência ou rota sem necessidade do redesign.

## 6. Matriz de validação para as etapas futuras

| Área | Casos mínimos e resultado esperado |
| --- | --- |
| Rotas e HTTP | Home, catálogo e quatro detalhes 200; slug oculto/inválido/desconhecido e rota inexistente 404; método não registrado continua 404 |
| Caminho-base | Raiz pública e `/portifolio`; links, scripts, CSS, ícones e âncoras sem prefixo perdido ou duplicado; manter também os casos de subdiretório já cobertos pela suíte |
| Conteúdo | Comparar valores de perfil, projetos, habilidades e contatos antes/depois; textos realocados continuam acessíveis; dois destaques, dois secundários, nenhum registro excluído/invisível exposto |
| Temas | Escuro/claro em todas as páginas; sistema claro/escuro, escolha salva, valor inválido, armazenamento bloqueado, recarga e navegação entre rotas |
| Responsividade | Larguras 360, 390, 768, 1024 e 1440 CSS px; ausência de overflow; alturas curtas e viewport mobile dinâmica sem corte; zoom 200% e reflow a 320 CSS px |
| Capturas | Antes/depois do próprio site em 390, 768 e 1440px; depois nos dois temas, mesma viewport e estado. Hero comparado por proporção e hierarquia à imagem local, sem exigir igualdade pixel a pixel |
| Teclado e semântica | Skip link, ordem de foco, headings/IDs únicos, nomes dos controles, Escape no menu, foco não encoberto pelo header, destinos de âncoras e ausência de controles ocultos focáveis |
| Contraste | Texto normal ≥ 4,5:1; texto grande e indicadores aplicáveis ≥ 3:1; links, tags, foco, hover e menu sobre o fundo real nos dois temas |
| Movimento | `prefers-reduced-motion` na carga e após mudança; sem animação/scroll suave forçado; partículas simplificadas no mobile e pausadas fora de tela/aba oculta |
| Falhas e opcionais | JS desativado ou script indisponível: conteúdo e navegação utilizáveis; foto, imagem, links, contato, tecnologias ou currículo ausentes sem falhas de layout |
| Segurança de saída | Escaping de textos, atributos e URLs; protocolos inválidos continuam omitidos; nenhuma configuração privada ou informação sensível exposta |
| SEO | Título/descrição profissionais por página, canonical/OG somente com origem válida, favicon/preview preservados e `noindex` no 404; sem inventar domínio, robots ou sitemap |
| Desempenho | Lighthouse mobile/desktop da home, catálogo e detalhe; comparação com base atual e com partículas desligadas; sem regressão perceptível de rolagem, CLS por dimensões omitidas ou tarefas longas recorrentes |
| Console e rede | Nenhum erro novo de JS, requisição 404 de asset ou aviso PHP; verificar links externos e registrar indisponibilidades sem substituir destinos por suposição |

Na suíte, substituir expectativas obsoletas de CTA no hero e retorno exclusivo ao catálogo por verificações do comportamento final. O teste atual de contraste usa regex global e um mapa por nome de token: com dois temas, o último valor sobrescreveria o primeiro. Separar os blocos/temas na avaliação para efetivamente validar ambos. Preservar as verificações de escaping; ao introduzir scripts legítimos, verificar ausência do payload malicioso em vez de proibir indiscriminadamente elementos `script`.

Acrescentar verificações úteis de ordem das seções, conservação dos textos, classificação/visibilidade dos projetos, dados sociais compartilhados, URLs de scripts e âncoras a partir de páginas internas. Interações de menu/tema, flash de carregamento e animação precisam de navegador; assertions de HTML não comprovam esses comportamentos. Não instalar um framework de testes para este redesign sem necessidade demonstrada.

Comandos previstos, na raiz (a suíte deve rodar preferencialmente em cópia temporária, pois escreve fixtures sob `public/`):

```sh
php -d error_reporting=E_ALL tests/run.php
find app public config tests -name '*.php' -print0 | xargs -0 -n1 php -l
php -l index.php
git diff --check
```

Integração HTTP e inspeção visual devem usar o ambiente documentado no README: Apache em `http://localhost/portifolio/` ou servidor PHP com `APP_BASE_PATH=''` e raiz `public/`. Registrar corpo e status das respostas: status 200 isolado não comprova execução PHP. Não alterar Apache ou permissões para realizar o redesign.

## 7. Riscos, dependências e decisões de implementação

| Risco ou dependência | Tratamento planejado |
| --- | --- |
| Foto pessoal e capturas ausentes | Fallback tipográfico no hero e projetos sem mídia; aceitar a composição sem bloquear implementação. Fotografias reais continuam pendentes do responsável |
| Guias com caminhos e estados históricos divergentes | Usar os arquivos efetivos listados na seção 1, conteúdo atual e decisões explícitas; não renomear fontes ou restaurar dados antigos |
| Hero amplo quebrar páginas internas | Separar regras da home e contêiner das seções; conferir catálogo/detalhe/404 em cada mudança de layout |
| Perda de introdução/objetivos ou ressalvas ao simplificar | Inventário e comparação dos valores; realocar sem reescrever; preservar `#objetivos` e descrições integrais |
| Tema incompleto/flash/armazenamento indisponível | Tokens para todas as superfícies e estados; inicialização anterior ao CSS, fallback seguro e testes com armazenamento bloqueado |
| Menu transparente ilegível ou sem espaço | Testar sobre partículas e nos dois temas; aplicar superfície ao rolar e nas internas; mudar breakpoint pelo conteúdo |
| Contatos indisponíveis fora da home | Dados compartilhados preparados pelo Controller com o Model existente; nenhuma URL fixa nos partials e nenhuma consulta nas Views |
| Partículas consumirem bateria ou capturarem interação | Canvas sem eventos de ponteiro, limites de densidade/DPR, pausas e remoção em movimento reduzido/mobile; conteúdo independente do efeito |
| Ícones trazerem pacotes, downloads ou inferências de competência | SVG local/inline com origem/licença adequada e fallback textual; nenhuma nova habilidade por associação visual |
| Testes presos à marcação anterior | Alterar somente expectativas de apresentação substituídas; manter os contratos de segurança, SEO, dados opcionais, visibilidade e roteamento |
| Confundir redesign com publicação | Não configurar domínio/hosting, habilitar o próprio projeto, revisar sistemas externos ou realizar deploy nesta sequência |

## 8. Verificações desta execução e pendências

Executado em 21/10/2026, antes da criação do documento:

- Leitura dos documentos efetivos, inventário de arquivos e inspeção de Controllers, Core, Models, Views, CSS, dados, roteamento, configuração de execução e testes.
- Abertura e análise visual da imagem local equivalente; consulta estrutural do site Brittany Chiang.
- `php -d error_reporting=E_ALL tests/run.php` em cópia temporária: **986/986 verificações aprovadas**. A cópia foi removida automaticamente; fixtures não foram executadas sobre a árvore original.
- Sintaxe de **29 arquivos PHP**, incluindo `app`, `public`, `config`, `tests` e entrada raiz: sem erros.
- `git diff --check`: sem problemas antes da documentação; repetir após gravar este plano.
- Estado inicial do Git: somente `config/design/` não rastreado. Esses arquivos fornecidos devem permanecer intactos.

Esta execução não realizou testes HTTP, inspeção visual da aplicação local, teclado, console, Lighthouse ou instalação de dependências. Não comprova o comportamento do redesign, que ainda não foi implementado. Resultados anteriores em README/PROGRESS são históricos.

Pendências: execução futura das etapas 1–9 e respectivas validações; foto e capturas reais autorizadas. E-mail, currículo, certificados complementares, domínio e publicação continuam com as restrições atuais e não bloqueiam o planejamento. Nenhum commit ou push foi realizado.
