## Provider para utilização de senha única USP no Laravel

Vídeos sobre a utilização desta biblioteca:

-   [1.x](https://youtu.be/jLFM2AUFJgw)
-   [2.x](https://www.youtube.com/watch?v=t6Zf3nK-oIo)
-   [3.x] ...

Dependências em PHP, além das default do laravel:

    php-curl

### Instalação

    composer require uspdev/senhaunica-socialite

### Configuração nova

#### Publique e rode as migrations

Esta biblioteca modifica a tabela `users` padrão do laravel acrescentando os campos `codpes` e `level`, e também modifica o campo `password` deixando-o opcional.

    php artisan vendor:publish --provider="Uspdev\SenhaunicaSocialite\SenhaunicaServiceProvider" --tag="migrations"

    php artisan migrate


#### Cadastre o `callback_id`

A url é o que está cadastrado no `APP_URL` mais `/callback`.

-   dev: https://dev.uspdigital.usp.br/adminws/oauthConsumidorAcessar
-   prod: https://uspdigital.usp.br/adminws/oauthConsumidorAcessar

#### Coloque variáveis no .env e .env.example

    # uspdev/senhaunica-socialite
    SENHAUNICA_KEY=fflch_sti
    SENHAUNICA_SECRET=sua_super_chave_segura
    SENHAUNICA_CALLBACK_ID=85

    # Esses usuários terão privilégios de admin
    SENHAUNICA_ADMINS=11111,22222,33333

    # Habilite para salvar o retorno em storage/app/debug/oauth/
    #SENHAUNICA_DEBUG=true

    # URL do servidor oauth no ambiente de dev
    #SENHAUNICA_DEV="https://dev.uspdigital.usp.br/wsusuario/oauth"

### Gates

Esta biblioteca fornece os gates `admin` e `user` como uma forma simples de autorização.


## Atualizando a partir da versão 2

Atualize o composer.json para usar a versão `"^3.0"`

Deve-se desfazer as alterações indicadas na versão 2 para usar as novas funcionalidades.

    app/Providers/EventServiceProvider.php, remover as linhas que chamam o SenhaunicaSocialite
    config/services.php, remover a seção senhaunica
    routes/web.php, remover as rotas login, callback e logout
    app/Http/Controllers/Auth/LoginController.php, apagar o arquivo
    App\Providers\AuthServiceProvider.php, remover gates admin e user

Confira o .env se está de acordo com as recomendações atuais.

## Informações para desenvolvedores(as):

### Direto na aplicação

Caso deseje ver todos parâmetros retornados no requisição, em Server.php:

```php
public function userDetails($data, TokenCredentials $tokenCredentials)
{
    dd($data);
}
```

### Debug

Outra possibilidade é configurar a variável `SENHAUNICA_DEBUG` como `true`. Isso salvará em JSON as informações obtidas de `<Servidor de OAuth1>/wsusuario/oauth/usuariousp` no diretório `storage/app/debug/oauth` por número USP.

Ex: para o número USP 3141592, os dados serão salvos em `storage/app/debug/oauth/3141592.json`.
