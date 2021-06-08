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

Esta biblioteca modifica a tabela `users` padrão do laravel acrescentando o campo `codpes` e modifica o campo `password` deixando-o opcional.

Caso você queira, pode usar a persistência da forma que for mais conveniente porém, para usar as rotas/controller internos você deve utilizar esta migration ou executar manualmente as alterações correspondentes. 

    php artisan vendor:publish --provider="Uspdev\SenhaunicaSocialite\SenhaunicaServiceProvider" --tag="migrations"

#### Configuração da biblioteca laravel-permission

A biblioteca [laravel-permission](https://github.com/spatie/laravel-permission/) é poderosa, flexível e bem estabelecida pela comunidade laravel no quesito grupos e permissões. Por padrão, os números USP inseridos em SENHAUNICA_ADMINS e SENHAUNICA_GERENTES ganharão as permissões admin e gerente respectivamente.
Todos usuários por padrão ganham a permission *user*. Essas permissões são automaticamente Gates, assim não é necessário definí-las em *AuthServiceProvider*.
Duas configurações são necessárias:

Publicar as migrations da laravel-permission:

    php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag="migrations"
    php artisan migrate

Dentro da classe do model User declarar:

    use \Spatie\Permission\Traits\HasRoles;

Neste momento você tem um poder enorme de regras de permissionamento no seu sistema, podendo criar outras permissions, agrupá-las em roles ou mesmo listar as permissões de um usuários, como:

    $user->getPermissionNames();

Ou listar todos usuários com uma dada permissão:

    $users = User::permission('admin')->get();


#### Cadastre o `callback_id`

A url é o que está cadastrado no `APP_URL` mais `/callback`.

-   dev: https://dev.uspdigital.usp.br/adminws/oauthConsumidorAcessar
-   prod: https://uspdigital.usp.br/adminws/oauthConsumidorAcessar

#### Coloque variáveis no .env e .env.example da sua aplicação

    # uspdev/senhaunica-socialite
    SENHAUNICA_KEY=fflch_sti
    SENHAUNICA_SECRET=sua_super_chave_segura
    SENHAUNICA_CALLBACK_ID=85

    # URL do servidor oauth no ambiente de dev
    #SENHAUNICA_DEV="https://dev.uspdigital.usp.br/wsusuario/oauth"

    # Habilite para salvar o retorno em storage/app/debug/oauth/
    #SENHAUNICA_DEBUG=true

    # Esses usuários terão privilégios especiais 
    #SENHAUNICA_ADMINS=11111,22222,33333
    #SENHAUNICA_GERENTES=4444,5555,6666

#### Arquivo de configuração

Caso você queira modificar o comportamento padrão de algumas partes como por exemplo, desabilitar os gates internos, publique o arquivo de configuração e ajuste conforme necessário. A publicação é necessária somente se for alterar alguma configuração.

    php artisan vendor:publish --provider="Uspdev\SenhaunicaSocialite\SenhaunicaServiceProvider" --tag="config"


#### Gates

Esta biblioteca fornece, por padrão, os gates `admin`, `gerente` e `user` como uma forma simples de autorização. Caso você não queira utilizar esses gates, desabilite no arquivo `config/senhaunica.php`. Use conforme a necessidade em sua aplicação.

* **user** é todo usuário autenticado 
* todo **admin** é **gerente** também
* **admins** e **gerentes** devem estar cadastrados no `.env` para serem populados no BD apropriadamente

#### Rotas e controllers

Essa biblioteca fornece rotas internas para login e logout e o respectivo controller para utilizar a senha única de forma **exclusiva**. Com isso, não é necessário criar essas rotas/controller manualmente. 

Caso sua aplicação exija um processo mais complexo, desative no arquivo `config/senhaunica.php`.

### Atualizando

A atualização para essa versão exije aguns ajustes no código.

Primeiramente atualize o `composer.json` para usar a nova versão e rode `composer install`

    "uspdev/senhaunica-socialite": "^3.0"

Deve-se desfazer/verificar **pelo menos** os seguintes arquivos:
* `app/Providers/EventServiceProvider.php`, remover as linhas que chamam o SenhaunicaSocialite
* `config/services.php`, remover a seção senhaunica
* Se não for usar os gates e rotas/controller internos, desative-os no `config/senhaunica.php`

Para usar os gates e rotas/controller internos verifique /ajuste os seguintes arquivos:
* `routes/web.php`, remover as rotas login, callback e logout
* `App/Http/Controllers/Auth/LoginController.php`, apagar o arquivo
* `App\Providers\AuthServiceProvider.php`, remover gates admin e user
* A tabela `users` deve possuir a coluna `codpes`. Se for o caso, publique a migration e ajuste o arquivo publicado conforme sua necessidade

Confira o .env se está de acordo com as recomendações atuais.

## Informações para desenvolvedores(as):

### Direto na aplicação

Caso deseje ver todos parâmetros retornados na requisição, em Server.php:

```php
public function userDetails($data, TokenCredentials $tokenCredentials)
{
    dd($data);
}
```

### Debug

Outra possibilidade é configurar a variável `SENHAUNICA_DEBUG` como `true`. Isso salvará em JSON as informações obtidas de `<Servidor de OAuth1>/wsusuario/oauth/usuariousp` no diretório `storage/app/debug/oauth` por número USP.

Ex: para o número USP 3141592, os dados serão salvos em `storage/app/debug/oauth/3141592.json`.
