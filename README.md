## Provider para utilização de senha única USP no Laravel 

Dependências em PHP:

    php-curl
    
## usage

Instalação:

    composer config repositories.SenhaunicaSocialite git https://github.com/uspdev/SenhaunicaSocialite.git
    composer require socialiteproviders/senhaunica:dev-master
    
No array $provider de config\app.php adicione a linha:
    
    \SocialiteProviders\Manager\ServiceProvider::class,
    
No array $listen app/Providers/EventServiceProvider.php adicione o array:

    \SocialiteProviders\Manager\SocialiteWasCalled::class => [
        'SocialiteProviders\Senhaunica\SenhaunicaExtendSocialite@handle',
    ],
    
Em config/services.php:

    'senhaunica' => [
        'client_id' => env('SENHAUNICA_KEY'),
        'client_secret' => env('SENHAUNICA_SECRET'),
         'redirect' => '/',
    ], 
    
    
Parâmetros no .env/.env.example:

    SENHAUNICA_KEY=fflch_sti
    SENHAUNICA_SECRET=gjgdfjk
    SENHAUNICA_CALLBACK_ID=85
    
Na migration que cria a tabela users

    $table->string('password')->nullable();
    $table->string('codpes');
    
Adicionar codpes no model User:

    protected $fillable = [
        'name', 'email', 'password','codpes',
    ];


Adiconar métodos em LoginController:

    use Socialite;
    public function redirectToProvider()
    {
        return Socialite::driver('senhaunica')->redirect();
    }

    public function handleProviderCallback()
    {
        $user = Socialite::driver('senhaunica')->user();

        $authUser = User::where('codpes', $user->id)->first();
        if ($authUser) {
            return $authUser;
        }
        return User::create([
            'name'     => $user->name,
            'email'    => $user->email,
            'codpes' => $user->id,
        ]);

        Auth::login($authUser, true);
        return redirect('/');
    }

Rotas:

    Route::get('login/senhaunica', 'Auth\LoginController@redirectToProvider');
    Route::get('login/senhaunica/callback', 'Auth\LoginController@handleProviderCallback');
    
# Extras

Caso deseje ver todos parâmetros retornados no requisição, em Server.php:

    public function userDetails($data, TokenCredentials $tokenCredentials)
    {  
        var_dump($data); die();
    }
