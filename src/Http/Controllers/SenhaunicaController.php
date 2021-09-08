<?php

namespace Uspdev\SenhaunicaSocialite\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

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

        if ($request->msg == 'noLocalUser') {
            return view('senhaunica::unavailable', ['reason' => 'noLocalUser']);
        }
        
        // guardando para onde vai retornar
        $redirect = $request->redirect ?? $request->headers->get('referer') ?? '/';
        $request->session()->push(config('senhaunica.session_key') . '.redirect', $redirect);

        return \Socialite::driver('senhaunica')->redirect();
    }

    public function handleProviderCallback(Request $request)
    {
        $userSenhaUnica = \Socialite::driver('senhaunica')->user();

        // se onlyLocalUsers = true, não vamos permitir usuários não cadastrados de logar
        if (config('senhaunica.onlyLocalUsers')) {
            $user = User::newLocalUser($userSenhaUnica->codpes);
            if (!$user) {
                session()->invalidate();
                session()->regenerateToken();
                return redirect('/login?msg=noLocalUser');
            }
        } else {
            $user = User::firstOrNew(['codpes' => $userSenhaUnica->codpes]);
        }

        // bind dos dados retornados
        $user->codpes = $userSenhaUnica->codpes;
        $user->email = $userSenhaUnica->email;
        $user->name = $userSenhaUnica->nompes;
        $user->save();

        // Vamos retornar uma tela amigável caso não tenha sido configurado a trait
        if ($this->missingTrait()) {
            return view('senhaunica::unavailable');
        }

        $user->setDefaultPermission();
        \Auth::login($user, true);

        $redirect = $request->session()->pull(config('senhaunica.session_key') . '.redirect', '/')[0];
        if (strpos($redirect, 'login') !== false) {
            $redirect = '/';
        } 
        return redirect($redirect);
    }

    public function logout()
    {
        \Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect('/');
    }

    /**
     * Verifica se a trait foi carregada no model User da aplicação final
     * Se nao foi carregado nem o login vai funcionar.
     */
    protected function missingTrait()
    {
        if (in_array('Uspdev\SenhaunicaSocialite\Traits\HasSenhaunica', array_keys((new \ReflectionClass('\App\Models\User'))->getTraits()))) {
            return false;
        } else {
            return true;
        }
    }
}
