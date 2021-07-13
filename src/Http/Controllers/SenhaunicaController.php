<?php

namespace Uspdev\SenhaunicaSocialite\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Uspdev\Replicado\Pessoa;

class SenhaunicaController extends Controller
{
    /**
     * Redireciona o login para o provider senhaunica
     *
     * Retorna para a url indicada ou para a página atual (referer).
     */
    public function redirectToProvider(Request $request)
    {
        $request->validate([
            'redirect' => 'nullable|string',
        ]);
        // guardando para onde vai retornar
        $request->session()->push(config('senhaunica.session_key') . '.redirect', $request->redirect ?? $request->headers->get('referer'));

        return \Socialite::driver('senhaunica')->redirect();
    }

    public function handleProviderCallback(Request $request)
    {
        $userSenhaUnica = \Socialite::driver('senhaunica')->user();
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
            } else {
                // vamos revogar o acesso se dropPermissions
                if (config('senhaunica.dropPermissions')) {
                    $user->revokePermissionTo('admin');
                }
            }

            // vamos verificar no config se o usuário é gerente
            if (in_array($userSenhaUnica->codpes, config('senhaunica.gerentes'))) {
                $user->givePermissionTo('gerente');
            } else {
                if (config('senhaunica.dropPermissions')) {
                    $user->revokePermissionTo('gerente');
                }
            }

            // default
            $user->givePermissionTo('user');
        }

        // bind dos dados retornados
        $user->codpes = $userSenhaUnica->codpes;
        $user->email = $userSenhaUnica->email;
        $user->name = $userSenhaUnica->nompes;
        $user->save();
        \Auth::login($user, true);

        return redirect($request->session()->pull(config('senhaunica.session_key') . '.redirect', '/')[0]);
    }

    public function logout()
    {
        \Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect('/');
    }

    /**
     * Assume identidade de outra pessoa
     */
    public function loginAs(Request $request)
    {
        $this->authorize('admin');
        $request->validate(['codpes' => 'required|integer']);

        $user = User::where('codpes', $request->codpes)->first();

        if (is_null($user)) {
            if (!class_exists('Uspdev\\Replicado\\Pessoa')) {
                $error = ['codpes' => 'Usuário não existe na base local'];
                return redirect()->back()->withErrors($error)->withInput();
            }

            if ($pessoa = Pessoa::dump($request->codpes)) {
                $user = new User;
                $user->codpes = $request->codpes;
                $user->name = $pessoa['nompesttd'];
                $user->email = Pessoa::retornarEmailUsp($request->codpes);
                $user->save();
            } else {
                $error = ['codpes' => 'Usuário não existe na base da USP'];
                return redirect()->back()->withErrors($error)->withInput();
            }
        }

        \Auth::login($user, true);
        return redirect('/');
    }

    public function loginAsForm() {
        $this->authorize('admin');
        return view('senhaunica::loginas-form');
    }
}
