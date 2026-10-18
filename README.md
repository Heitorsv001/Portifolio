# Portfólio MVC em PHP

Esqueleto inicial de um site em PHP organizado no padrão MVC, sem framework ou banco de dados.

## Requisitos e instalação

- PHP 8.2 ou superior.
- [Composer 2](https://getcomposer.org/download/).

Na raiz do projeto, execute:

```sh
composer install
```

O Composer gera `vendor/autoload.php` com o mapeamento PSR-4 de `App\` para `app/`. Não versione `vendor/`. Nenhuma biblioteca externa é necessária nesta etapa.

## Estrutura

- `app/Controllers`: recebe requisições e coordena a resposta.
- `app/Core`: Controller base e roteador GET.
- `app/Models`: regras e acesso aos dados.
- `app/Views`: templates HTML.
- `public/`: ponto de entrada e arquivos públicos (CSS, JavaScript e imagens).
- `config/app.php`: título da aplicação e caminho-base.
- `tests/run.php`: testes isolados de roteamento e renderização, sem framework.
- `storage/logs/`: destino reservado para logs da aplicação.

## Acesso local

Depois de instalar o autoload, execute na raiz do projeto:

```sh
APP_BASE_PATH='' php -S 127.0.0.1:8000 -t public
```

Acesse `http://127.0.0.1:8000/`. O servidor embutido é apenas para desenvolvimento.

O acesso Apache existente em `http://localhost/portifolio/` permanece suportado. O caminho-base padrão é `/portifolio`; pode ser alterado pela variável de ambiente `APP_BASE_PATH`, disponibilizada ao processo PHP. O `.htaccess` encaminha requisições ao front controller e bloqueia acesso direto aos diretórios privados e metadados do projeto. Esse modo exige `mod_rewrite` e permissão de leitura do `.htaccess` pelo Apache.

Em produção, configure a raiz pública do servidor para `public/` e `APP_BASE_PATH` como vazio quando o site estiver na raiz do domínio. O servidor PHP embutido com `-t public` também mantém os arquivos privados fora da raiz pública. Não foi alterada a configuração do servidor nesta etapa.

## Rotas da etapa 1

- `GET /`: view de teste da estrutura MVC.
- Rota desconhecida ou método não registrado: página com status HTTP 404.

O Controller solicita a View; o Router apenas encontra e executa a ação. Conteúdo dinâmico e atributos HTML são escapados nas Views. Falhas de inicialização retornam uma mensagem genérica com status 500 e registram detalhes no log do PHP.

## Verificações

```sh
composer validate --no-check-publish
composer dump-autoload --optimize --strict-psr
php tests/run.php
find app public config tests -name '*.php' -print0 | xargs -0 -n1 php -l
php -l index.php
```

Os testes isolados carregam as classes diretamente: verificam rotas, prefixo da instalação, métodos HTTP, escaping e renderização de 404, sem substituir a validação do autoload Composer ou do servidor web.

Com o servidor local em execução, confira a integração:

```sh
curl -i http://127.0.0.1:8000/
curl -i http://127.0.0.1:8000/rota-inexistente
curl -i http://127.0.0.1:8000/assets/css/style.css
```

Os resultados esperados são, respectivamente, status 200 com a view de teste, status 404 e status 200 com o CSS. Verifique também a abertura da página e o carregamento do estilo no navegador.

## Validação da etapa 1 — 18/10/2026

Patch `portfolio-etapa1.patch` aplicado sem conflitos. Composer instalado temporariamente em `/tmp/portfolio-composer.phar`, com integridade do instalador conferida pela assinatura SHA-384 oficial; `composer.lock` e `vendor/autoload.php` gerados.

- Composer install, validate e dump-autoload otimizado com `--strict-psr`: aprovados. Único aviso de validação: licença ainda não definida pelo proprietário.
- `php tests/run.php`: 43/43 verificações aprovadas.
- Sintaxe dos 10 arquivos PHP: sem erros.
- Integração real com servidor PHP e autoload Composer: home 200, rota inexistente 404 e CSS 200.
- Na verificação inicial do Apache em `http://localhost/portifolio/`, CSS retornou 200 e `config/app.php` retornou 403, mas o PHP era entregue como texto. Após receber as instruções para habilitar PHP-FPM, o proprietário confirmou que a correção funcionou. Essa confirmação não substitui uma nova verificação automatizada das rotas no Apache.
- Renderização visual, responsividade e console não puderam ser inspecionados: nenhum navegador está disponível na sessão. A view de teste não contém JavaScript nem elementos interativos para validar por teclado.

Os critérios funcionais da etapa 1 foram confirmados no servidor PHP documentado acima. O problema de execução PHP no Apache foi resolvido conforme confirmação do proprietário; a revisão visual completa permanece pendente. Nenhuma tarefa da etapa 2 foi iniciada.
