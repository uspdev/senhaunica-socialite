<?php

namespace Uspdev\SenhaunicaSocialite\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Uspdev\Replicado\Pessoa;

class UserController extends Controller
{
    public function loginAsForm()
    {
        $this->authorize('admin');

        return view('senhaunica::loginas');
    }

    /**
     * Assume identidade de outra pessoa
     */
    public function loginAs(Request $request)
    {
        $this->authorize('admin');
        $request->validate(['codpes' => 'required|integer']);

        $user = User::findOrCreateFromReplicado($request->codpes);
        if (!($user instanceof \App\Models\User)) {
            return redirect()->back()->withErrors(['codpes' => $user])->withInput();
        }
        $user->setDefaultPermission();
        \Auth::login($user, true);
        return redirect('/');
    }

    public function users()
    {
        $this->authorize('admin');

        \UspTheme::activeUrl('users');

        return view('senhaunica::users', [
            'users' => User::all(),
        ]);
    }

    public function updatePermission(Request $request, $id)
    {
        $this->authorize('admin');

        $request->validate([
            'level' => 'required|in:admin,gerente,user',
        ]);

        # vamos definir a nova permissão e remover todas as outras
        $user = User::find($id);
        $user->revokePermissionTo(['user', 'gerente', 'admin']);
        $user->givePermissionTo([$request->level]);

        return back();
    }

    public function getJsonModalContent($id)
    {
        $this->authorize('admin');

        $user = User::find($id);

        if ($user->hasSenhaunicaJson()) {
            return view('senhaunica::partials.json-modal-content', [
                'user' => $user,
                'json' => Storage::get('debug/oauth/' . $user->codpes . '.json'),
                'date' => date('d/m/Y H:i:s', Storage::lastModified('debug/oauth/' . $user->codpes . '.json')),
            ]);
        }
    }

}
