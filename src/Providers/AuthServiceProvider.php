<?php

namespace Uspdev\SenhaunicaSocialite\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        Gate::define('admin', function ($user) {
            return $user->level == 'admin' ? true : false;
        });

        Gate::define('gerente', function ($user) {
            return ($user->level == 'gerente' ||  $user->level == 'admin') ? true : false;
        });
        
        Gate::define('user', function ($user) {
            return $user;
        });
    }
}
