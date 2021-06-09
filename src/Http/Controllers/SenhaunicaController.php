<?php

namespace Uspdev\SenhaunicaSocialite\Http\Controllers;

use App\Models\User;
use Auth;
use Socialite;
use Spatie\Permission\Models\Permission;

class SenhaunicaController extends Controller
{
    public function redirectToProvider()
    {
        return Socialite::driver('senhaunica')->redirect();
    }

    public function handleProviderCallback()
    {
        $userSenhaUnica = Socialite::driver('senhaunica')->user();
        $user = User::firstOrNew(['codpes' => $userSenhaUnica->codpes]);

        if (config('senhaunica.permission')) {
            // garantindo que as permissions existam
            $permissions = ['admin', 'gerente', 'user'];
            foreach ($permissions as $permission) {
                Permission::findOrCreate($permission);
            }

            // vamos verificar no config se o usuário é admin
            if (in_array($userSenhaUnica->codpes, config('senhaunica.admins'))) {
                $user->givePermissionTo('admin');
            }

            // vamos verificar no config se o usuário é gerente
            if (in_array($userSenhaUnica->codpes, config('senhaunica.gerentes'))) {
                $user->givePermissionTo('gerente');
            }

            // default
            $user->givePermissionTo('user');
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
