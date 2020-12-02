## Provider para utilização de senha única USP no Laravel

Vídeos sobre a utilização desta biblioteca:

 - [1.x](https://youtu.be/jLFM2AUFJgw)
 - [2.x](https://www.youtube.com/watch?v=t6Zf3nK-oIo)

Dependências em PHP, além das default do laravel:

    php-curl

## Uso

Instalação:

    composer require uspdev/senhaunica-socialite
    
Exemplo de como o array `$listen` em `app/Providers/EventServiceProvider.php`
deve carregar o driver `senhaunica`:

    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            'Uspdev\SenhaunicaSocialite\SenhaunicaExtendSocialite@handle',
        ],
    ];

Em `config/services.php:

    'senhaunica' => [
        'client_id' => env('SENHAUNICA_KEY'),
        'client_secret' => env('SENHAUNICA_SECRET'),
        'callback_id' => env('SENHAUNICA_CALLBACK_ID'),
        'dev' => env('SENHAUNICA_DEV','no'),
        'redirect' => '/',
    ], 

Cadastre o Callback Id:

- dev: https://dev.uspdigital.usp.br/adminws/oauthConsumidorAcessar
- prod: https://uspdigital.usp.br/adminws/oauthConsumidorAcessar

Parâmetros no .env:

    SENHAUNICA_KEY=fflch_sti
    SENHAUNICA_SECRET=sua_super_chave_segura
    SENHAUNICA_CALLBACK_ID=85

O seguinte parâmetro diz se você quer trabalhar no ambiente dev:

    SENHAUNICA_DEV="https://dev.uspdigital.usp.br/wsusuario/oauth"


É necessário ao menos duas rotas:

    Route::get('login/senhaunica', [LoginController::class, 'redirectToProvider']);
    Route::get('callback', [LoginController::class, 'handleProviderCallback']);

Adiconar métodos em LoginController:

    <?php

    namespace App\Http\Controllers\Auth;

    use App\Http\Controllers\Controller;
    use App\Providers\RouteServiceProvider;

    use Socialite;
    use App\Models\User;
    use Auth;
    use Illuminate\Http\Request;

    class LoginController extends Controller
    {

        public function __construct()
        {
            $this->middleware('guest');
        }

        public function redirectToProvider()
        {
            return Socialite::driver('senhaunica')->redirect();
        }

        public function handleProviderCallback()
        {
            $userSenhaUnica = Socialite::driver('senhaunica')->user();
            $user = User::where('codpes',$userSenhaUnica->codpes)->first();

            if (is_null($user)) $user = new User;

            // bind do dados retornados
            $user->codpes = $userSenhaUnica->codpes;
            $user->email = $userSenhaUnica->email;
            $user->name = $userSenhaUnica->nompes;
            $user->save();
            Auth::login($user, true);
            return redirect('/');
        }
    }

# Informações para desenvolverdores(as):

## Direto na aplicação

Caso deseje ver todos parâmetros retornados no requisição, em Server.php:

    public function userDetails($data, TokenCredentials $tokenCredentials)
    {  
        dd($data);
    }

## Debug
Outra possibilidade é configurar a variável `APP_DEBUG` como `true`. Isso salvará em JSON as informações obtidas de `<Servidor de OAuth1>/wsusuario/oauth/usuariousp` no diretório `storage/app/debug/oauth` por número USP.

Ex: para o número USP 3141592, os dados serão salvos em `storage/app/debug/oauth/3141592.json`.
