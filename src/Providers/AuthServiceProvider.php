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
        // Implicitly grant "Super Admin" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()
        Gate::before(function ($user, $ability) {
            return in_array('admin', $user->getPermissionNames()->toArray()) ? true : null;
        });

        // carregando users no menu, se disponível
        if (class_exists('\UspTheme')) {
            \UspTheme::addMenu('senhaunica-socialite', [
                'text' => '<i class="fas fa-users-cog"></i> Users',
                'url' => config('senhaunica.userRoutes'),
                'can' => 'admin',
            ]);
        }

    }
}
