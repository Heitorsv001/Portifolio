# Progresso e passagem de contexto

Atualizado em **18/10/2026**. Este registro descreve o trabalho e as evidências desta sessão; o ambiente deve ser conferido novamente na próxima sessão.

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
