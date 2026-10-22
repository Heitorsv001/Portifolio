DESIGN_GUIDE — Redesign do portfólio pessoal
Versão: 1.0
Status: especificação para planejamento e implementação incremental com Codex
Escopo: interface, experiência de navegação, responsividade e acessibilidade do portfólio PHP MVC existente
Documentos complementares: AGENTS.md, PROJECT_CONTEXT.md, PORTFOLIO_CONTENT_GUIDE.md, PROGRESS.md (se houver)
1. Objetivo e princípios
Renovar a apresentação visual do portfólio com uma identidade escura, minimalista, tecnológica e profissional, mantendo legibilidade, navegação simples e destaque para experiência real e projetos. Preservar PHP com arquitetura MVC, conteúdo atual, rotas, modelos e funcionalidades já implementadas. O redesign não é uma reescrita do sistema.

Princípios, em ordem de prioridade:
1. Clareza de conteúdo: informações profissionais verificadas devem ser fáceis de localizar e ler.
2. Identidade própria: referências inspiram composição, ritmo e interações; não copiar textos, marca ou layout inteiro.
3. Acessibilidade: sem perda de contraste, navegação por teclado e respeito à preferência por movimento reduzido.
4. Desempenho: partículas e animações não podem prejudicar celular ou equipamentos modestos.
5. Manutenção: criar tokens CSS e componentes/partials compartilhados, sem duplicar estilos.
Decisões de design confirmadas pelo responsável
Dimensão	Decisão
Aparência	Escuro como padrão + alternância para tema claro
Destaque	Roxo, em quantidade moderada
Personalidade	Equilíbrio entre profissional e tecnológico
Fontes	Fonte de interface legível + monoespaçada para metadados/detalhes
Hero	Ocupa a tela no desktop; mais compacto no celular
Foto	Circular, centralizada, acima do nome
Subtítulo final	“Desenvolvedor Web e estudante” (sem mencionar IFMS no subtítulo)
Partículas	Movimento lento, resposta discreta ao mouse
Ações no hero	Somente ícones sociais; não adicionar botões CTA
Estrutura	Uma página principal com âncoras e páginas individuais de projeto
Navegação desktop	Menu transparente sobre a hero
Navegação mobile	Menu hambúrguer acessível
Prioridade de conteúdo	Após a apresentação, destacar experiência profissional / estágio
Certificações	Lista discreta; emissor e data apenas quando confirmados
Projetos	Destaques visuais amplos + lista compacta de secundários
Detalhes de projeto	Resumo, tecnologias e link para GitHub; sem páginas excessivamente extensas
Experiência	Bloco com período, cargo, descrição e tecnologias
Habilidades	Ícones + nomes, agrupados por categoria
Animações	Sutis e suaves
Cursor	Padrão; somente hover em elementos interativos
Celular	Reduzir partículas e animações por desempenho
Rodapé	Discreto, com nome e ícones das redes sociais


Regra de precedência: estas decisões explícitas substituem qualquer proposta preliminar, prompt antigo ou interpretação das referências que entre em conflito.
2. Referências e seu papel
2.1 Hero: screenshot de Diogo Correia
Arquivo local: design/references/hero-reference.png
Site apresentado na imagem: diogotc.com
Usar para: proporção da apresentação, alinhamento central, foto circular, título amplo, símbolos sociais e ambiente de pontos/linhas sobre fundo escuro.

Extrair:
- fundo quase preto com pontos pequenos e conexões muito sutis;
- ampla área de respiro ao redor da apresentação;
- foto circular centralizada acima do nome;
- nome em primeiro plano, subtítulo menor;
- fila pequena de ícones de redes abaixo do subtítulo;
- menu simples e leve sobre o hero.
Não copiar: nome/foto original, ícones de plataformas não usadas, textos, identidade própria do autor, posições em pixels, ou a mesma distribuição exata das partículas.

Nota: a screenshot contém barras do sistema operacional e do navegador; essas partes não integram o design do site.
2.2 Conteúdo: Brittany Chiang
URL: https://brittanychiang.com/
Usar para: hierarquia de texto, densidade adequada, leitura por blocos, seção de experiência com período/cargo/descrição/tags e projetos com apresentação profissional.

Extrair:
- composição editorial e conteúdos escaneáveis;
- divisão clara entre apresentação, experiência e projetos;
- boa hierarquia entre título, metadados e descrições;
- tecnologia associada a cada experiência/projeto;
- interações de hover discretas e navegação consistente.
Não copiar: texto, marca, fotografias, estrutura idêntica de colunas, lista de experiências da autora ou componentes proprietários.

Conciliação: o hero segue a primeira referência; todo o conteúdo abaixo dele usa o rigor editorial e a organização da segunda referência. Não substituir o hero central por um layout de introdução lateral por causa da segunda referência.
3. Identidade visual e tokens
As escolhas abaixo são valores iniciais propostos para implementação, não reprodução exata das referências. Ajustes finos só após avaliação visual.
3.1 Cores
Token	Tema escuro (padrão)	Tema claro
--color-bg	#17171C	#F8F8FC
--color-surface	#22222B	#FFFFFF
--color-surface-hover	#2B2A37	#F0EDF7
--color-text	#F3F1FA	#211B2D
--color-muted	#B6B2C1	#595468
--color-border	#363240	#DDD8E7
--color-accent	#B39AFB	#6844B8
--color-accent-soft	rgba(179,154,251,.14)	rgba(104,68,184,.09)
--color-focus	#D4C6FF	#5E35A8


- Roxo para links, foco, tags e destaques pontuais; evitar grandes superfícies roxas.
- Não usar cor como único indicador de estado/ação.
- Garantir contraste de texto mínimo WCAG AA em superfícies reais (4,5:1 no texto normal; 3:1 em texto grande e componentes aplicáveis). Se uma combinação falhar, ajustar os tons e documentar a alteração.
- Partículas no tema escuro: pontos em cinza lavanda desaturado, linhas com opacidade baixa. No tema claro: pontos cinza/roxo suave, linhas ainda mais discretas.
Implementação sugerida:
:root,
[data-theme="dark"] {
  color-scheme: dark;
  --color-bg: #17171c;
  --color-surface: #22222b;
  --color-surface-hover: #2b2a37;
  --color-text: #f3f1fa;
  --color-muted: #b6b2c1;
  --color-border: #363240;
  --color-accent: #b39afb;
  --color-accent-soft: rgba(179, 154, 251, .14);
  --color-focus: #d4c6ff;
}
[data-theme="light"] {
  color-scheme: light;
  --color-bg: #f8f8fc;
  --color-surface: #fff;
  --color-surface-hover: #f0edf7;
  --color-text: #211b2d;
  --color-muted: #595468;
  --color-border: #ddd8e7;
  --color-accent: #6844b8;
  --color-accent-soft: rgba(104, 68, 184, .09);
  --color-focus: #5e35a8;
}
3.2 Tipografia
- Principal: Inter ou fonte sans-serif semelhante; preferir pilha local de fallback se não for carregar fonte externa.
- Monoespaçada: JetBrains Mono, IBM Plex Mono ou ui-monospace, aplicada a datas, pequenos rótulos, índices e tags técnicas com moderação.
- Não usar fonte monoespaçada em parágrafos longos.
- Escala desktop sugerida: H1 do hero clamp(2.8rem, 6vw, 4.8rem); H2 clamp(1.8rem, 3vw, 2.6rem); H3 1.25rem; corpo 1rem–1.075rem; metadados 0.85–0.9rem.
- Altura de linha corpo entre 1.65–1.8; títulos entre 1.1–1.25.
- Evitar texto inteiro em caixa alta (exceto rótulos curtos).
3.3 Grade e espaçamentos
- Container central de conteúdo: max-width: 1100px, padding horizontal 24px desktop / 20px mobile.
- Medida de parágrafos: max-width: 68ch.
- Espaçamentos sugeridos: 8, 12, 16, 24, 32, 48, 64, 96px.
- Seções desktop com 80–112px entre blocos; mobile 56–72px.
- Bordas suaves 10–16px para cards. Preferir poucos cards com bordas discretas em vez de layout inteiro em caixas.
4. Arquitetura de informação
4.1 Página inicial (/)
Ordem recomendada:
1. Hero — nome, foto circular, subtítulo final e ícones sociais.
2. Experiência profissional — estágio de Desenvolvimento Web no IFMS; destaque editorial.
3. Sobre mim — estudante ativo do Técnico Integrado em Informática no IFMS; texto real já documentado.
4. Projetos em destaque — Gerenciador de Permanências, Crowns e Portfólio MVC em PHP somente quando preenchido/publicado.
5. Outros projetos — Festividades de Fim de Ano e Notícias.
6. Habilidades — grupos com ícones e nomes, inclusive ferramentas de IA quando confirmadas.
7. Formação e certificações — formação em andamento e quatro certificados identificados, sem inventar emissor/datas.
8. Contato/rodapé — texto discreto, ícones sociais e link de retorno ao topo.
A ordem pode ser ajustada apenas com justificativa, mas a experiência deve ser a primeira seção de conteúdo após o hero, por escolha explícita.

Observação de conteúdo: o subtítulo do hero é “Desenvolvedor Web e estudante”; o IFMS aparece no Sobre mim, em Experiência e em Formação — não inserir “IFMS” no subtítulo.
4.2 Páginas de projeto (/projetos/{slug})
- Manter layout visual compartilhado, header simples e rodapé.
- Link visível “Voltar aos projetos” (retorna à âncora do catálogo na página inicial).
- Exibir título, resumo objetivo, tecnologias com nomes, link GitHub e imagem real apenas se disponível e autorizada.
- Opcional: 2–4 pontos de funcionalidade confirmada. Não transformar detalhes em artigos extensos.
- Não renderizar demo_url se não existir; não inventar screenshot ou dados.
- Manter 404 visual coerente para slug inválido.
4.3 Navegação e âncoras
Desktop:
- Logo/nome discreto à esquerda; itens à direita: Experiência, Sobre, Projetos, Habilidades, Formação, Contato (avaliar condensação se não couber).
- Navbar transparente sobre a hero, com texto de alto contraste.
- Ao rolar, aplicar leve superfície translúcida ou fundo sólido suave se necessário à legibilidade; evitar efeito de vidro excessivo.
- Navegação por âncoras com scroll-margin-top para não ocultar títulos.
- Botão de alternância claro/escuro com nome acessível; respeitar prioridade de foco.
Mobile:
- Mostrar nome/logotipo e botões de tema + hambúrguer.
- Botão com aria-expanded, aria-controls e rótulo claro “Abrir menu”/“Fechar menu”.
- Menu em painel compacto com links de âncora, sem bloquear permanentemente o conteúdo.
- Fechar ao escolher destino, ao pressionar Escape e quando o foco sair de forma apropriada.
- Gerenciar foco com cuidado se o painel for modal; bloquear scroll somente enquanto menu modal estiver aberto.
- Não depender de hover para acessar funcionalidades.
5. Hero: especificação detalhada
Desktop
- Altura: min-height: 100svh (usar fallback adequado) descontando apenas se necessário a navbar.
- Conteúdo centralizado vertical e horizontalmente, acima de uma camada visual não interativa.
- Foto circular 88–112px com object-fit: cover, borda sutil; usar foto fornecida pelo responsável, nunca avatar de terceiros. Se indisponível, usar avatar tipográfico neutro até receber imagem.
- Nome: Heitor Silva Vareiro, texto grande, sem efeitos ofuscantes.
- Subtítulo: Desenvolvedor Web e estudante.
- Ícones sociais apenas sob o subtítulo: GitHub e LinkedIn; opcionalmente e-mail somente com confirmação de divulgação.
- Não adicionar botões “Ver projetos”, “Sobre mim”, “Baixar currículo” ou outros CTAs dentro do hero.
- Ícones sociais também devem aparecer no rodapé, mesmo já presentes no hero.
- Opcional: indicação discreta de rolagem sem aparência de CTA; evitar se competir com a composição.
Partículas
- Visual: pontos pequenos, conexões finas somente em distâncias curtas; baixa densidade e baixo contraste.
- Movimento: lento, uniforme; reação sutil quando ponteiro está sobre a área, sem partículas perseguindo o cursor ou saltos bruscos.
- Camada atrás do conteúdo, pointer-events: none, aria-hidden="true".
- Limitar densidade por área de tela e pixel ratio; considerar Canvas 2D simples se já não houver solução adequada no projeto.
- Pausar desenho quando documento não estiver visível (se usado requestAnimationFrame) e ao sair da viewport quando possível.
- Não exigir framework gráfico nem nova biblioteca sem justificativa.
- Em prefers-reduced-motion: reduce, parar animação e oferecer fundo estático ou partículas estáticas.
- Desabilitar ou simplificar drasticamente no mobile; não comprometer rolagem nem bateria.
- Garantir legibilidade mesmo sem JavaScript e durante carregamento.
Mobile
- Hero mais compacto: tamanho baseado no conteúdo (min-height: 75svh como ponto inicial, mas evitar cortes).
- Foto 72–88px; nome quebra naturalmente em 1–2 linhas com espaço adequado.
- Ícones com área de toque mínima recomendada de 44x44px.
- Reduzir partículas e remover reação ao ponteiro em telas sem mouse.
- Respeitar áreas seguras e viewport dinâmica.
6. Seções de conteúdo
6.1 Experiência
Bloco editorial inspirado na organização da Brittany:
- coluna/meta: período mar/2026 – out/2026 (conforme currículo, sem alegar continuidade);
- conteúdo: Estagiário de Desenvolvimento Web — IFMS;
- texto: desenvolvimento e manutenção do site institucional do Arco Literário — Clube de Leitura Caio Fernando de Abreu;
- atividades: refatoração, correção de bugs e melhorias de segurança;
- tecnologias: Node.js, Express, MongoDB, EJS, Bootstrap 5.
Sem inventar métricas, resultados nem cargo efetivo. Em telas estreitas, meta acima do texto. Não confundir Arco Literário com repositório pessoal.
6.2 Sobre mim
Usar o conteúdo revisado do PORTFOLIO_CONTENT_GUIDE.md e preservar em texto corrido a frase que informa que o responsável cursa o Técnico Integrado em Informática no IFMS. Ser objetivo; limitar largura dos parágrafos e usar links contextuais com discrição.
6.3 Projetos
Destaques: layout horizontal ou editorial, com imagem real de um lado e texto de outro; alternância lateral opcional, sem prejudicar consistência. Em mobile, empilhar imagem e texto.
- Gerenciador → Gerenciador de Permanências: destaque com cautela sobre autenticação; não prometer produção pronta.
- Crowns → Crowns: destaque; execução/compilação e persistência não totalmente verificadas.
- Portifolio → Portfólio MVC em PHP: destaque após preenchido e publicado.
Secundários: lista mais compacta, com título, descrição curta, tecnologias e link para detalhes/GitHub:
- festividades → Festividades de Fim de Ano: formulário demonstrativo (não envio real).
- Noticias → Notícias: site estático.
Não mostrar: Gerenciador-de-permanencias, Festividades-fim-de-ano, Crowns_tlp2, taskeasy-HeitorVareiro, testeEngenharia, server, minecraft ou README de perfil como projetos pessoais.
6.4 Habilidades
Ícones acompanhados de texto sempre; nunca somente logotipos. Grupos sugeridos:
- Linguagens: Java, PHP, JavaScript.
- Web: HTML, CSS, Node.js, Express, EJS, Bootstrap.
- Dados: PostgreSQL, MongoDB.
- Desktop: JavaFX.
- Ferramentas: Git, GitHub, Maven, Composer.
- Desenvolvimento assistido por IA: Claude Code, Codex (se o responsável mantiver esta preferência).
Evitar notas de proficiência percentuais. Ícones locais ou biblioteca já instalada; não acrescentar dezenas de pacotes ou imagens externas de fontes desconhecidas. Usar fallback textual se o ícone falhar.
6.5 Formação e certificações
Formação: Técnico Integrado em Informática — IFMS, 2024–2026 (previsão); em andamento até confirmação de conclusão.

Certificações:
- Introdução à Ciência de Dados;
- JavaScript (avançado);
- Claude Academy: Claude Code 101;
- JavaScript (básico).
Lista discreta. Exibir emissor, data, link ou selo de verificação apenas após confirmação. Nunca gerar ano aproximado.
6.6 Rodapé
- Pequeno e discreto, com Heitor Silva Vareiro e ícones de GitHub e LinkedIn.
- Opcional: texto curto de autoria; não incluir frases grandiosas nem links sem destino.
- Sempre visível em páginas individuais de projeto.
7. Componentes e estados
Criar ou reutilizar componentes/partials de: Header, ThemeToggle, MobileNav, Hero, SocialLinks, SectionHeading, ExperienceItem, FeaturedProject, ProjectListItem, SkillGroup, CertificationItem, Footer e layout das páginas de detalhes.
- Links: sublinhado ou mudança de cor suave com foco visível; não ocultar função atrás de hover.
- Cards: hover de deslocamento vertical de até 2–4px e mudança sutil de superfície/borda; sem parallax excessivo.
- Ícones: tamanho visual 18–22px, área de clique maior.
- Foco: :focus-visible em todos os controles.
- Estados ausentes: esconder imagem/demo/campo opcional sem espaços vazios.
- Estados 404: mensagem clara, navegação de retorno.
- Ícones decorativos: aria-hidden; links somente-ícone precisam de nome acessível.
8. Tema claro/escuro
- Inicialmente aplicar preferência salva pelo usuário (se disponível e válida); caso contrário usar prefers-color-scheme; na ausência, padrão escuro.
- A escolha manual deve ter prioridade sobre preferência do sistema em sessões seguintes.
- Persistência por localStorage, com tratamento de indisponibilidade, sem impedir o site de renderizar.
- Evitar flash de tema incorreto antes do CSS principal, respeitando CSP e estrutura já existente.
- O botão deve ter estado e rótulo acessíveis (Alternar para tema claro / Alternar para tema escuro).
- Todos os elementos devem usar tokens; evitar cores fixas em componentes que quebrem o tema alternativo.
- Validar contraste, partículas, estados de hover, foco e imagens nos dois temas.
9. Responsividade e movimento
Breakpoints sugeridos: <= 640px (mobile), 641–900px (tablet), > 900px (desktop). Ajustar com base no conteúdo, não exclusivamente nos dispositivos.
- Testar pelo menos nas larguras 360, 390, 768, 1024, 1440px.
- Não permitir rolagem horizontal.
- Menu vira hambúrguer no mobile, com teclado, toque e fechamento apropriados.
- Cards destacados passam a uma coluna em telas estreitas.
- Reduzir efeitos de fundo e animações em dispositivos móveis.
- Transições típicas entre 160–260ms em opacity, transform, background-color, border-color; preferir transform e opacity quando houver animação.
- Respeitar prefers-reduced-motion: reduce globalmente, incluindo partículas, scroll e revelação de elementos.
- Não esconder conteúdo até animação disparar; ele precisa permanecer visível mesmo se JS falhar.
10. Acessibilidade e qualidade
- HTML semântico: header, nav, main, section, article, footer e hierarquia de headings consistente.
- Link de pular para o conteúdo principal.
- Contraste WCAG AA, inclusive no menu transparente sobre partículas.
- alt significativo para foto e capturas; ícones decorativos sem texto acessível redundante.
- Navegação completa por teclado; teste de Escape e foco no menu mobile.
- Toques confortáveis, sem áreas clicáveis muito pequenas.
- Efeitos jamais bloqueiam seleção de texto, rolagem ou acionamento de links.
- Carregamento de imagens com dimensões declaradas, loading="lazy" fora do hero e formatos otimizados.
- Não incluir imagens da referência como recurso final do site.
- Revisar legibilidade, links externos, metadados e experiência com JavaScript desativado quando aplicável.
11. Regras técnicas para o Codex
1. Ler e respeitar AGENTS.md, PROJECT_CONTEXT.md e PORTFOLIO_CONTENT_GUIDE.md antes de modificar o site.
2. Inspecionar o código existente primeiro: views, CSS, JS, rotas, modelos, dados e testes.
3. Não substituir PHP MVC por SPA, framework JS, motor de templates diferente ou outro backend.
4. Priorizar estilos CSS próprios e o JavaScript mínimo necessário.
5. Centralizar tokens em arquivo ou seção de CSS compartilhado; criar partials apenas onde reduzam duplicação real.
6. Não alterar textos profissionais, datas ou classificações de projetos sem consultar o guia de conteúdo.
7. Não alterar READMEs dos repositórios de projetos, banco de dados, autenticação nem back-end por motivo meramente visual.
8. Não baixar imagens alheias; usar somente ativos fornecidos/autorizados e capturas reais.
9. Não criar git add, commits ou pushes sem autorização explícita.
10. Fazer mudanças por etapas verificáveis; resumir arquivos afetados, testes, diferenças visuais e pendências.
12. Plano de implementação incremental
Cada etapa termina com uma revisão visual antes de avançar. O Codex deve criar design/REDESIGN_PLAN.md com diagnóstico do código atual, mapeamento de arquivos e riscos antes de implementar as etapas abaixo.
Etapa	Entrega	Critério de aceite
0. Diagnóstico	Inventário do layout, CSS, JS e rotas; plano local	Nenhum código alterado
1. Tokens e temas	Paleta e tipografia, alternância escuro/claro	Sem flash evidente, preferência respeitada, contraste revisado
2. Header e navegação	Menu transparente desktop, hambúrguer mobile	Links funcionam, teclado/Escape funcionam, foco visível
3. Hero	Foto circular, nome, subtítulo final, ícones sociais	Centralizado, ocupa viewport desktop, mobile sem cortes e sem CTA
4. Partículas	Fundo discreto animado, reativo ao mouse	Performance aceitável; desligado/reduzido em mobile e movimento reduzido
5. Experiência e sobre	Estágio em primeiro lugar, texto de estudante ativo	Dados fiéis, legibilidade e boa hierarquia
6. Projetos	Destaques + secundários, detalhes integrados	Links reais, cards sem demos/imagens inventadas, páginas existentes intactas
7. Habilidades e formação	Ícones e nomes; certificações discretas	Sem níveis fictícios nem dados de certificado inventados
8. Rodapé	Nome e redes sociais	Links em todas as páginas; sem conteúdo duplicado excessivo
9. Polimento	Responsividade, acessibilidade, SEO e testes	Sem overflow, foco, contraste, movimento e links validados


Nota de dependência: foto pessoal e capturas reais são ativos externos ainda não anexados ao repositório; implementar fallback elegante sem inventar imagens.
13. Testes e validação visual
Preparar screenshots de comparação do próprio projeto, nas larguras 390px, 768px e 1440px, nos temas claro e escuro; para hero, comparar composição com hero-reference.png, não exigir fidelidade pixel a pixel.

Checklist de aceite final:
- [ ] Site usa PHP MVC existente sem alteração indevida de rotas ou Models.
- [ ] Hero desktop ocupa uma tela e mobile é mais compacto.
- [ ] Foto circular acima do nome, com fallback honesto se indisponível.
- [ ] Subtítulo exatamente “Desenvolvedor Web e estudante”.
- [ ] Sem botões CTA no hero; somente ícones sociais.
- [ ] Ícones sociais presentes também no rodapé.
- [ ] Estágio aparece imediatamente após o hero.
- [ ] IFMS aparece em Sobre mim, Experiência e Formação, não no subtítulo.
- [ ] Menu transparente no desktop, hambúrguer acessível no mobile.
- [ ] Tema escuro e claro funcionam e respeitam preferência salva.
- [ ] Fundo com partículas é discreto e não interfere no conteúdo.
- [ ] Movimento reduzido respeitado; mobile simplificado.
- [ ] Projetos destacados amplos e lista secundária compacta.
- [ ] Páginas individuais mostram resumo, tecnologias e GitHub.
- [ ] Ícones de habilidades sempre acompanhados de nomes.
- [ ] Certificações sem emissores ou datas inventadas.
- [ ] Nenhuma versão arquivada exibida.
- [ ] Contraste, teclado, 404, sem overflow e links revisados.
- [ ] Arquivos alterados e verificações executadas documentados.
- [ ] Nenhum commit/push sem autorização.
14. Prompt pronto para o Codex — planejamento inicial
Leia AGENTS.md, PROJECT_CONTEXT.md, PORTFOLIO_CONTENT_GUIDE.md e design/DESIGN_GUIDE.md.
Analise a screenshot em design/references/hero-reference.png e o site https://brittanychiang.com/ como referências visuais com papéis distintos.

Planeje o redesign do projeto PHP MVC EXISTENTE. Respeite integralmente as decisões explícitas do DESIGN_GUIDE.md, especialmente:
- tema escuro com alternância para claro, destaque roxo;
- hero desktop em tela inteira, foto circular, subtítulo EXATO "Desenvolvedor Web e estudante" e somente ícones sociais;
- partículas lentas e discretas, reduzidas em mobile;
- menu transparente no desktop, hambúrguer acessível no mobile;
- experiência profissional como primeira seção após o hero;
- projetos em destaque amplos e secundários em lista;
- ícones sociais também no rodapé;
- página inicial única com páginas individuais de projeto.

Nesta primeira execução, NÃO modifique código. Faça um inventário dos arquivos existentes e crie design/REDESIGN_PLAN.md com: diagnóstico, mapeamento de arquivos, etapas, riscos, critérios de aceite e verificações necessárias. Não recrie conteúdo profissional e não faça commits/pushes.
15. Prompt para executar cada etapa aprovada
Leia AGENTS.md, PROJECT_CONTEXT.md, PORTFOLIO_CONTENT_GUIDE.md,
design/DESIGN_GUIDE.md e design/REDESIGN_PLAN.md.

Implemente APENAS a etapa [NÚMERO/NOME] do plano aprovado.
Inspecione o código existente e preserve PHP MVC, rotas, dados profissionais e demais funcionalidades.
Não adicione dependências sem justificativa; não modifique etapas futuras.
Execute testes e verificações relevantes. Ao finalizar, informe arquivos alterados,
comportamento nos temas claro/escuro e mobile, critérios atendidos e pendências.
Não crie commits nem faça push sem autorização.
Fonte editorial: decisões fornecidas pelo responsável neste diálogo, screenshot de referência e conteúdo profissional já organizado em PORTFOLIO_CONTENT_GUIDE.md. Valores numéricos e tokens sugeridos neste guia são propostas de implementação a validar visualmente, não escolhas expressas pelo responsável.