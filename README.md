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

Caso você queira, pode usar a persistência da forma que for mais conveniente porém, para usar as rotas e controller internos você deve utilizar esta migration ou executar manualmente as alterações correspondentes. 

    php artisan vendor:publish --provider="Uspdev\SenhaunicaSocialite\SenhaunicaServiceProvider" --tag="migrations"

    php artisan migrate


#### Arquivo de configuração

Caso você queira usar as rotas e controller internos publique o arquivo de configuração e ajuste conforme necessário. Para usar os gates internos também é neessário habilitá-lo no arquivo de configuração.

    php artisan vendor:publish --provider="Uspdev\SenhaunicaSocialite\SenhaunicaServiceProvider" --tag="config"

#### Cadastre o `callback_id`

A url é o que está cadastrado no `APP_URL` mais `/callback`.

-   dev: https://dev.uspdigital.usp.br/adminws/oauthConsumidorAcessar
-   prod: https://uspdigital.usp.br/adminws/oauthConsumidorAcessar

#### Coloque variáveis no .env e .env.example da sua aplicação

    # uspdev/senhaunica-socialite
    SENHAUNICA_KEY=fflch_sti
    SENHAUNICA_SECRET=sua_super_chave_segura
    SENHAUNICA_CALLBACK_ID=85

    # Habilite para salvar o retorno em storage/app/debug/oauth/
    #SENHAUNICA_DEBUG=true

    # URL do servidor oauth no ambiente de dev
    #SENHAUNICA_DEV="https://dev.uspdigital.usp.br/wsusuario/oauth"

    # Esses usuários terão privilégios especiais 
    # se senhaunica.gates = true
    #SENHAUNICA_ADMINS=11111,22222,33333
    #SENHAUNICA_GERENTES=4444,5555,6666


#### Gates

Esta biblioteca fornece, opcionalmente, os gates `admin`, `gerente` e `user` como uma forma simples de autorização. Para habilitar ajuste apropriadamente no arquivo `config/senhaunica.php` Use conforme a necessidade em sua aplicação.

* user é todo usuário autenticado 
* todo admin é gerente também
* admins e gerentes devem estar cadastrados no .env

#### Rotas e controllers

Essa biblioteca fornece rotas internas para login e logout e o respoectivo controller. Para usá-los habilite no arquivo `config/senhaunica.php`.

### Atualizando

Caso queira utilizar os recursos internos de rotas e gates você deve remover as referências prévias.

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
