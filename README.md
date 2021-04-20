## Provider para utilização de senha única USP no Laravel

Vídeos sobre a utilização desta biblioteca:

- [1.x](https://youtu.be/jLFM2AUFJgw)
- [2.x](https://www.youtube.com/watch?v=t6Zf3nK-oIo)

Dependências em PHP, além das default do laravel:

    php-curl

## Uso

Instalação:

    composer require uspdev/senhaunica-socialite

## Arquivos a serem ajustados

Alguns dos ajustes são obrigatórios, outros opcionais. Adapte-os à sua necessidade.

#### Arquivo `app/Providers/EventServiceProvider.php`

Exemplo de como o array `$listen` deve carregar o driver `senhaunica`:

```php
protected $listen = [
    Registered::class => [
        SendEmailVerificationNotification::class,
    ],

    \SocialiteProviders\Manager\SocialiteWasCalled::class => [
        'Uspdev\SenhaunicaSocialite\SenhaunicaExtendSocialite@handle',
    ],
];
```

#### Arquivo `config/services.php`

```php
'senhaunica' => [
    'client_id' => env('SENHAUNICA_KEY'),
    'client_secret' => env('SENHAUNICA_SECRET'),
    'callback_id' => env('SENHAUNICA_CALLBACK_ID'),
    'dev' => env('SENHAUNICA_DEV','no'),
    'redirect' => '/',
    'admins' => env('SENHAUNICA_ADMINS'),
    'debug' => (bool) env('SENHAUNICA_DEBUG', false),
],
```

#### Arquivo `routes/web.php`

Adicionar as rotas. É necessário ao menos `login` e `callback`. Um `logout` também é bom:

```php
use App\Http\Controllers\Auth\LoginController;
...
Route::get('login', [LoginController::class, 'redirectToProvider']);
Route::get('callback', [LoginController::class, 'handleProviderCallback']);
Route::post('logout', [LoginController::class, 'logout']);
```

#### Arquivo `app/Http/Controllers/Auth/LoginController.php`

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use Socialite;

class LoginController extends Controller
{

    public function redirectToProvider()
    {
        return Socialite::driver('senhaunica')->redirect();
    }

    public function handleProviderCallback()
    {
        $userSenhaUnica = Socialite::driver('senhaunica')->user();
        $user = User::firstOrNew(['codpes' => $userSenhaUnica->codpes]);

        // vamos verificar no config se o usuário é admin
        if (strpos(config('services.senhaunica.admins'), $userSenhaUnica->codpes) !== false) {
            $user->level = 'admin';
        }

        // bind dos dados retornados
        $user->codpes = $userSenhaUnica->codpes;
        $user->email = $userSenhaUnica->email;
        $user->name = $userSenhaUnica->nompes;
        $user->save();
        Auth::login($user, true);
        return redirect('/');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
```

#### Arquivo `database/migrations/...00_create_users_table.php`

É necessário criar alguns campos no BD. Se é um projeto novo, edite direto no arquivo, se não crie uma migration para as alterações. Não esqueça de rodar `php artisan migrate` ou `php artisan migrate:fresh` depois das alterações.

```php
$table->string('password')->nullable(); # deixar opcional

$table->integer('codpes');
$table->string('level')->nullable();
```

#### Arquivo `App\Providers\AuthServiceProvider.php`

Opcionalmente, adicione alguns gates no método `boot()`:

```php
Gate::define('admin', function ($user) {
    return $user->level == 'admin' ? true : false;
});

Gate::define('user', function ($user) {
    return $user;
});
```

#### Cadastre o `callback_id`

A url é o que está cadastrado no `APP_URL` mais `/callback`.

- dev: https://dev.uspdigital.usp.br/adminws/oauthConsumidorAcessar
- prod: https://uspdigital.usp.br/adminws/oauthConsumidorAcessar

#### Parâmetros no .env e .env.example:

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
