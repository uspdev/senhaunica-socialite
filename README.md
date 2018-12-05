## Provider para utilização de senha única USP no Laravel

Hangout sobre a utilização desta biblioteca

[![Hangout senha única laravel](https://img.youtube.com/vi/jLFM2AUFJgw/0.jpg)](https://youtu.be/jLFM2AUFJgw)

Dependências em PHP:

    php-curl

## Usage

Instalação:

    composer require uspdev/senhaunica-socialite
    
Exemplo de como o array $listen em *app/Providers/EventServiceProvider.php*
deve carregar o driver senhaunica:

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
         'redirect' => '/',
    ], 

Parâmetros no .env:

    SENHAUNICA_KEY=fflch_sti
    SENHAUNICA_SECRET=gjgdfjk
    SENHAUNICA_CALLBACK_ID=85

Adiconar métodos em LoginController:

    use Socialite;
    public function redirectToProvider()
    {
        return Socialite::driver('senhaunica')->redirect();
    }

    public function handleProviderCallback()
    {
        $user = Socialite::driver('senhaunica')->user();
        // aqui vc pode inserir o usuário no banco de dados local, fazer o login etc.
    }

Rotas:

    Route::get('login/senhaunica', 'Auth\LoginController@redirectToProvider');
    Route::get('login/senhaunica/callback', 'Auth\LoginController@handleProviderCallback');

# Informations to developers:

Caso deseje ver todos parâmetros retornados no requisição, em Server.php:

    public function userDetails($data, TokenCredentials $tokenCredentials)
    {  
        var_dump($data); die();
    }
