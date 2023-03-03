<?php

namespace Uspdev\SenhaunicaSocialite\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Uspdev\Replicado\Pessoa;

class UserController extends Controller
{
    public function loginAsForm()
    {
        $this->authorize('admin');
        if (hasUspTheme()) {
            \UspTheme::activeUrl(route('SenhaunicaLoginAsForm'));
        }
        return view('senhaunica::loginas');
    }

    /**
     * Assume identidade de outra pessoa
     */
    public function loginAs(Request $request)
    {
        $this->authorize('admin');
        $request->validate(['codpes' => 'required|integer']);

        session()->push(config('senhaunica.session_key') . '.undo_loginas', \Auth::user()->codpes);

        $user = User::findOrCreateFromReplicado($request->codpes);
        if (!($user instanceof \App\Models\User)) {
            return redirect()->back()->withErrors(['codpes' => $user])->withInput();
        }
        $user->setDefaultPermission();
        \Auth::login($user, true);
        return redirect('/');
    }

    /**
     * Se está com identidade de outro, permite retornar à sua
     */
    public function undoLoginAs()
    {
        $this->authorize('user');
        $codpes = session()->pull(config('senhaunica.session_key') . '.undo_loginas');
        if (!$codpes) {
            return redirect()->back()->withErrors(['codpes' => 'Undo indisponível']);
        }
        $user = User::where('codpes', $codpes)->first();
        $user->setDefaultPermission();
        \Auth::login($user, true);
        return redirect('/');
    }

    public function index()
    {
        $this->authorize('admin');
        if (hasUspTheme()) {
            \UspTheme::activeUrl('users');
        }
        return view('senhaunica::users', [
            'users' => User::orderBy('name')->paginate(),
            'columns' => User::getColumns(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('admin');

        $request->validate([
            'codpes' => 'required|integer',
            'level' => 'required|in:admin,gerente,user',
            'loginas' => ['nullable', Rule::in([1])],
        ]);

        $user = User::findOrCreateFromReplicado($request->codpes);
        if (!($user instanceof \App\Models\User)) {
            return redirect()->back()->withErrors(['codpes' => $user])->withInput();
        }
        $user->setDefaultPermission();
        $user->givePermissionTo(
            Permission::where('name', $request->level)->first()
        );

        // vamos assumir identidade também ?
        if ($request->loginas) {
            session()->push(config('senhaunica.session_key') . '.undo_loginas', \Auth::user()->codpes);
            \Auth::login($user, true);
            return redirect('/');
        }

        return back();
    }

    /**
     *  Remove usuário da base local
     */
    public function destroy(Request $request, int $user_id)
    {
        $this->authorize('admin');

        if (config('senhaunica.destroyUser')) {
            User::find($user_id)->delete();
            return back();
        } else {
            return back()->withErrors('Remover usuário desabilitado no config!');
        }
    }

    /**
     * Grava/atualiza as permissões do usuário
     */
    public function updatePermission(Request $request, $id)
    {
        $this->authorize('admin');

        $request->validate([
            'level' => 'required|in:admin,gerente,user',
        ]);

        # vamos definir a nova permissão e remover todas as outras
        $user = User::find($id);
        $user->revokePermissionTo(['user', 'gerente', 'admin']);
        $user->givePermissionTo(
            Permission::where('name', $request->level)->first()
        );

        return back();
    }

    /**
     * Retorna o json formatado para ser incluído no modal
     *
     * @param $id
     * @return String
     */
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

    /**
     * Busca para ajax do select2 de adicionar pessoas
     */
    public function find(Request $request)
    {
        $this->authorize(config('senhaunica.findUsersGate'));

        if (!$request->term) {
            return response([]);
        }

        $results = [];

        if (hasReplicado()) {

            if (is_numeric($request->term)) {
                // procura por codpes
                $pessoa = Pessoa::dump($request->term);
                $results[] = [
                    'text' => $pessoa['codpes'] . ' ' . $pessoa['nompesttd'],
                    'id' => $pessoa['codpes'],
                ];
            } else {
                // procura por nome, usando fonético e somente ativos
                $pessoas = Pessoa::procurarPorNome($request->term);

                // limitando a resposta em 50 elementos
                $pessoas = array_slice($pessoas, 0, 50);

                $pessoas = collect($pessoas)->unique()->sortBy('nompesttd');

                // formatando para select2
                foreach ($pessoas as $pessoa) {
                    $results[] = [
                        'text' => $pessoa['codpes'] . ' ' . $pessoa['nompesttd'],
                        'id' => $pessoa['codpes'],
                    ];
                }
            }
        }

        return response(compact('results'));
    }

    public function search(Request $request)
    {
        $this->authorize('admin');
        if (empty($request->filter)) {
            return redirect()->route('senhaunica-users.index');
        }

        $users = User::query();
        foreach (User::getColumns() as $column) {
            $users->orWhere($column['key'], 'LIKE', '%' . $request->filter . '%');
        }

        return view('senhaunica::users', [
            'users' => $users->orderBy('name')->paginate(),
            'search' => $request->except('_token'),
            'columns' => User::getColumns(),
        ]);
    }

}
