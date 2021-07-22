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
        // guardando para onde vai retornar
        $request->session()->push(config('senhaunica.session_key') . '.redirect', $request->redirect ?? $request->headers->get('referer'));

        return \Socialite::driver('senhaunica')->redirect();
    }

    public function handleProviderCallback(Request $request)
    {
        $userSenhaUnica = \Socialite::driver('senhaunica')->user();
        $user = User::firstOrNew(['codpes' => $userSenhaUnica->codpes]);

        // bind dos dados retornados
        $user->codpes = $userSenhaUnica->codpes;
        $user->email = $userSenhaUnica->email;
        $user->name = $userSenhaUnica->nompes;
        $user->save();

        if ($this->missingTrait()) {
            return view('senhaunica::unavailable');
        }
        $user->setDefaultPermission();
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
