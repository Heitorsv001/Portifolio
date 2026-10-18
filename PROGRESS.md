# Progresso e passagem de contexto

Atualizado em **18/10/2026**. A seção 0 registra o estado atual após a etapa 3. As demais seções preservam o histórico das etapas 1 e 2 e devem ser interpretadas como estados anteriores. Conferir novamente o ambiente na próxima sessão.

## 0. Entrega atual — etapa 3: página inicial e apresentação pessoal

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
