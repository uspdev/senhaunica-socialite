<?php

namespace Uspdev\SenhaunicaSocialite\Providers;

use App\Models\User;
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
        // nas permissoes hierárquicas, o de cima tem todas as habilidades dos de baixo
        // a lógica aqui é ex. 'manager': verifica 'admin' || 'boss' || 'manager'
        foreach (User::$permissoesHierarquia as $key => $hierarquiaPerm) {
            Gate::define($hierarquiaPerm, function ($user) use($key) {
                $ret = null;
                for ($i = 0;$i <= $key; $i++) {
                    $ret = $ret || $user->hasPermissionTo(User::$permissoesHierarquia[$i], User::$hierarquiaNs);
                }
                return $ret;
            });
        }

        // cria os gates de permissoes por vínculo
        foreach (User::$permissoesVinculo as $vinculoPerm) {
            Gate::define('senhaunica.' . strtolower($vinculoPerm), function ($user) use ($vinculoPerm) {
                return $user->hasPermissionTo($vinculoPerm, User::$vinculoNs) ? true : null;
            });
        }

    }
}
