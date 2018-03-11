## Provider para utilização de senha única USP no Laravel 

Dependências em PHP:

    php-curl
    
## usage

Instalação:

    composer config repositories.senhaunica git https://github.com/uspdev/senhaunica.git
    composer require socialiteproviders/senhaunica:dev-master
    
No array $provider de config\app.php adicione a linha:
    
    \SocialiteProviders\Manager\ServiceProvider::class,
    
No array $listen app/Providers/EventServiceProvider.php adicione o array:

    \SocialiteProviders\Manager\SocialiteWasCalled::class => [
        'SocialiteProviders\senhaunica\SenhaunicaExtendSocialite@handle',
    ],
    
Em config/services.php:

    'senhaunica' => [
        'client_id' => env('SENHAUNICA_KEY'),
        'client_secret' => env('SENHAUNICA_SECRET'),
        'redirect' => env('SENHAUNICA_REDIRECT_URI'),  
    ], 
    
    
    <?php

Adiconar métodos no Controller de login:

    namespace App\Http\Controllers\Auth;
    use Socialite;

    class LoginController extends Controller
    {
        public function redirectToProvider()
        {
            return Socialite::driver('senhaunica')->redirect();
        }

        public function handleProviderCallback()
        {
            $user = Socialite::driver('github')->user();
            // $user->token;
        }
    }

Rotas:

    Route::get('login/senhaunica', 'Auth\LoginController@redirectToProvider');
    Route::get('login/senhaunica/callback', 'Auth\LoginController@handleProviderCallback');
