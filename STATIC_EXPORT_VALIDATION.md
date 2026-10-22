# Validação local da exportação estática

Execução em 22/10/2026, cobrindo as etapas 1–5 de `STATIC_DEPLOY_PLAN.md`. Destino prioritário Cloudflare Pages, sem configuração de conta, workflows ou publicação.

## Resultado

`dist/` contém seis páginas públicas e a página de erro, sete assets necessários, sitemap e robots: **16 arquivos**, sem PHP ou internos da aplicação. Slugs e conteúdo preservados; `portfolio-mvc-php` permanece oculto. O artefato usa caminhos na raiz e origem ilustrativa `https://portfolio.example`. Reconstruir com o endereço público definido antes da publicação.

O exportador captura HTML e status de subprocessos do front controller, coleta dependências públicas referenciadas, valida o resultado e só então substitui a árvore anterior. Não faz requisições à hospedagem nem deploy. O MVC normal conserva URLs e renderização anteriores.

## Correção prévia de currículo

Reproduzida a falha inicial de `tests/run.php`: 1.600/1.601. O registro da etapa 8 do redesign em `PROGRESS.md` e a view atual confirmam o currículo como item da lista de contatos. A lista com um download não está vazia. Alterada apenas a asserção para exigir exatamente um item com URL e atributo de download; apresentação e PDF não foram alterados. Após a correção, antes do exportador: 1.601/1.601.

## Verificações executadas

| Verificação | Resultado |
| --- | --- |
| Suíte MVC PHP com `E_ALL`, em cópia temporária | 1.601/1.601 |
| `tests/static-export.php`, com fixtures isoladas | 135/135 |
| `tests/theme.js` | 72/72 |
| `tests/navigation.js` | 29/29 |
| `tests/particles.js` | 38/38 |
| Sintaxe PHP em app/config/public/tests/scripts e entrada da raiz | 36 arquivos sem erro |
| Build Cloudflare na raiz e validação independente | Seis páginas + erro, sete assets, 16 arquivos |
| HTTP/Playwright/Chrome em `tests/static-browser.js` | 172/172 |
| Comparações de screenshot PHP/estático | 32/32 idênticas em bytes |

A integração PHP cobre raiz, `/Portifolio` e prefixo composto; .nojekyll apenas no perfil GitHub; sitemap e metadados; comparação dos bytes de assets; reconstrução idêntica; projeto habilitado/ocultado somente em fixture; remoção de páginas/assets antigos; cópia recursiva de CSS/fontes. Também verifica origem/base/opções inválidas, slugs inválidos/duplicados, 500 e warnings, link/asset quebrado, versões inválidas, tentativas de publicar PHP/credenciais/arquivos extras, links simbólicos, destino inválido e exportação concorrente. Os testes exigem falha explícita, manutenção da saída anterior e limpeza de staging.

O navegador comparou home, catálogo, detalhe de Crowns e 404 em 320, 390, 768 e 1440px, nos temas claro/escuro. Cada par PHP/estático foi capturado com movimento reduzido para eliminar animações da comparação. Não houve diferença visual ou overflow nos casos observados.

Conferidos ainda os status de todas as rotas públicas e do projeto oculto, acesso sem barra, 404 profunda com a view real, MIME/bytes de todos os assets, Tab/Enter no skip link, Enter/Escape e foco do menu, navegação por âncora, tema após reload, nome/bytes do PDF baixado, conteúdo sem JavaScript e ausência de erros inesperados de console/rede. As partículas foram testadas separadamente com movimento permitido: pixels desenhados no Canvas, liberação do buffer com movimento reduzido e retomada ao remover essa preferência.

O servidor estático do teste entrega apenas `dist/`, com 404 configurada no próprio teste; outro processo serve o MVC para comparação. O PHP web foi iniciado com `APP_STATIC_EXPORT=1` no ambiente, confirmando que a flag CLI não modifica a aplicação web. Ambos os servidores ficam em localhost e são encerrados junto do navegador.

## Ambiente e reprodução

- PHP CLI 8.4.27 e autoload Composer já disponível. Composer não foi executado; não houve alteração de manifesto/lock ou dependências da aplicação.
- `ext-dom` estava ausente. Pacote Debian `php8.4-xml` 8.4.27 foi obtido, conferido por SHA-256 e extraído em `/tmp/portfolio-php-xml`, sem instalar no sistema. Configuração temporária `/tmp/portfolio-php-ini/dom.ini` carrega o módulo correspondente ao PHP local.
- Node 24.19.0 e Playwright do runtime de ferramentas, com `/usr/bin/google-chrome`. Não foi criado pacote npm no projeto.
- Teste HTTP/navegador executado fora do sandbox após bloqueio `listen EPERM`; o processo ficou restrito a localhost.

Com `ext-dom` normalmente habilitada, usar os comandos do README. Neste ambiente específico, os comandos PHP de exportação foram executados com o prefixo:

```sh
PHP_INI_SCAN_DIR=:/tmp/portfolio-php-ini APP_BASE_PATH='' APP_ORIGIN='https://portfolio.example' php scripts/export-static.php --target=cloudflare
PHP_INI_SCAN_DIR=:/tmp/portfolio-php-ini APP_BASE_PATH='' APP_ORIGIN='https://portfolio.example' php scripts/validate-static.php
PHP_INI_SCAN_DIR=:/tmp/portfolio-php-ini php tests/static-export.php
```

Os caminhos em `/tmp` são temporários e específicos desta sessão. Em outro ambiente, habilitar a extensão DOM compatível com o PHP instalado; não copiar módulos binários entre versões diferentes. O runtime de hospedagem estática não precisa de PHP, DOM, Node ou Composer.

O teste de navegador é reproduzível com Playwright e navegador disponíveis, usando `CHROME_BIN`, `NODE_PATH` quando necessário e as variáveis do build. Quatro capturas de evidência foram guardadas em `/tmp/portfolio-static-visual/`: home de 390/1440px nos dois temas. Amostras desktop escura e mobile clara foram abertas para inspeção. As capturas são temporárias; o teste pode recriá-las via `STATIC_SCREENSHOT_DIR`.

## Limites e pendências

- O artefato está pronto para revisão estática, com a origem de exemplo explícita. A publicação exige novo build com origem definitiva.
- Cloudflare Pages e GitHub Pages não foram acessados/configurados. Redirecionamentos e status/MIME reais do provedor devem ser validados após futura publicação autorizada. A 404 local não comprova a configuração remota.
- A validação de prefixos foi feita pela integração PHP; a matriz visual/HTTP desta execução foi feita na raiz, que é o destino prioritário.
- Novas rotas fixas precisam ser incluídas no manifesto de build; projetos visíveis são enumerados automaticamente. O coletor suporta referências HTML/CSS e não descobre arquivos carregados dinamicamente por futuros scripts.
- Links externos foram preservados, mas sua disponibilidade não foi consultada nesta tarefa. Não houve envio de e-mail/formulário, auditoria Lighthouse nova, teste de tecnologia assistiva ou medição de desempenho remoto.
- A substituição da árvore local usa renomeação da saída anterior e da staging, sob lock. `dist/` é artefato de build, não um diretório para servir produção durante a reconstrução. Uma interrupção abrupta do processo/SO pode deixar staging ou backup; não há recuperação automática desses resíduos.

Nenhum `git add`, commit, push, workflow de publicação, deploy ou mudança de configuração do servidor foi realizado.
