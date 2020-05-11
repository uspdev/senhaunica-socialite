## Provider para utilização de senha única USP no Laravel

Hangout sobre a utilização desta biblioteca:

[![Hangout senha única laravel](https://img.youtube.com/vi/jLFM2AUFJgw/0.jpg)](https://youtu.be/jLFM2AUFJgw)

Dependências em PHP, além das default do laravel:

    php-curl

## Uso

Instalação:

    composer require uspdev/senhaunica-socialite
    
Exemplo de como o array $listen em *app/Providers/EventServiceProvider.php*
deve carregar o driver *senhaunica*:

    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            'Uspdev\SenhaunicaSocialite\SenhaunicaExtendSocialite@handle',
        ],
    ];

Em config/services.php:

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

O seguinte parâmetro diz se você quer trabalhar no ambiente dev (https://dev.uspdigital.usp.br/):

    SENHAUNICA_DEV=yes

É necessário ao menos duas rotas:

    Route::get('login/senhaunica', 'Auth\LoginController@redirectToProvider');
    Route::get('login/senhaunica/callback', 'Auth\LoginController@handleProviderCallback');

Adiconar métodos em LoginController:

    <?php

    namespace App\Http\Controllers\Auth;

    use App\Http\Controllers\Controller;
    use App\Providers\RouteServiceProvider;
    use Illuminate\Foundation\Auth\AuthenticatesUsers;

    use Socialite;
    use App\User;
    use Auth;
    use Illuminate\Http\Request;

    class LoginController extends Controller
    {

        use AuthenticatesUsers;
        protected $redirectTo = '/';

        public function __construct()
        {
            $this->middleware('guest')->except('logout');
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

        public function logout(Request $request) {
            Auth::logout();
            return redirect('/');
        }
    }

# Informações para desenvolverdores(as):

Caso deseje ver todos parâmetros retornados no requisição, em Server.php:

    public function userDetails($data, TokenCredentials $tokenCredentials)
    {  
        dd($data);
    }
