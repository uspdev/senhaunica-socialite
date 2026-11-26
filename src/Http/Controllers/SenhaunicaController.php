<?php

namespace Uspdev\SenhaunicaSocialite\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Uspdev\SenhaunicaSocialite\Events\SenhaunicaUsuarioLogado;

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
        $redirect = $request->redirect ?? $request->session()->get('url.intended') ?? '/';
        $request->session()->push(config('senhaunica.session_key') . '.redirect', $redirect);

        try {
            return \Socialite::driver('senhaunica')->redirect();
        } catch (\Throwable $e) {
            return $this->servicoIndisponivel($e);
        }
    }

    public function handleProviderCallback(Request $request)
    {
        try {
            $userSenhaUnica = \Socialite::driver('senhaunica')->user();
        } catch (\Throwable $e) {
            return $this->servicoIndisponivel($e);
        }

        // Vamos retornar uma tela amigável caso não tenha sido configurado a trait
        if ($this->missingTrait()) {
            return view('senhaunica::unavailable');
        }

        // se onlyLocalUsers = true, não vamos permitir usuários não cadastrados de logar
        if (config('senhaunica.onlyLocalUsers')) {
            if (!User::verificaUsuarioLocal($userSenhaUnica->codpes)) {
                session()->invalidate();
                session()->regenerateToken();
                return redirect('/login?msg=noLocalUser');
            }
        }

        $user = User::firstOrNew(['codpes' => $userSenhaUnica->codpes]);
        // bind dos dados retornados
        $user->codpes = $userSenhaUnica->codpes;
        $user->email = $userSenhaUnica->email
            ?: $userSenhaUnica->emailUsp
            ?: $userSenhaUnica->emailAlternativo
            ?: 'invalido' . $user->codpes . '@usp.br';
        $user->name = $userSenhaUnica->nompes;
        $user->save();

        \Auth::login($user, config('senhaunica.rememberSession'));
        //TODO: devemos gerar um log dos logins/logouts

        if (config('senhaunica.permission')) {
            $user->aplicarPermissoes($userSenhaUnica);
        }

        $redirect = $request->session()->pull(config('senhaunica.session_key') . '.redirect', '/')[0];
        if (strpos($redirect, 'login') !== false) {
            $redirect = '/';
        }
        
        // Dispatch the event
        event(new SenhaunicaUsuarioLogado($user, $userSenhaUnica, 'senhaunica'));
        
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
     * Falha na comunicação com o servidor OAuth da USP, em vez do erro 500.
     *
     * Exemplos: servidor responde 500 ("Error em obter request token"), fora do ar/timeout,
     * ou callback sem as credenciais temporárias na sessão (sessão expirada, voltar no navegador).
     * O erro continua registrado no log da aplicação.
     */
    protected function servicoIndisponivel(\Throwable $e)
    {
        report($e);

        return response()->view('senhaunica::unavailable', ['reason' => 'authServerDown'], 503);
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
