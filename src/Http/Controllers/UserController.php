<?php

namespace Uspdev\SenhaunicaSocialite\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
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

        $user = User::findOrCreateFromReplicado($request->codpes);
        if (!($user instanceof \App\Models\User)) {
            return redirect()->back()->withErrors(['codpes' => $user])->withInput();
        }

        session()->push(config('senhaunica.session_key') . '.undo_loginas', \Auth::user()->codpes);
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
        \Auth::login($user, true);
        return redirect('/');
    }

    /**
     * Mostra lista de usuários
     */
    public function index()
    {
        $this->authorize('admin');
        if (hasUspTheme()) {
            \UspTheme::activeUrl(config('senhaunica.userRoutes'));
        }

        return view('senhaunica::users', [
            'users' => User::orderBy('name')->paginate(),
            'columns' => User::getColumns(),
            'permissoesAplicacao' => Permission::where('guard_name', 'app')->get(),
            'rolesAplicacao' => Role::where('guard_name', 'app')->get(),
        ]);
    }

    /**
     * Cria novo registro a partir do replicado
     *
     * Precisa do replicado pois o usuário vai ser criado manualmente
     */
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

        $user->givePermissionTo(
            Permission::where('guard_name', 'web')->where('name', 'user')->first()
        );
        // aqui precisa dar permissão correspondente aos vínculos ativos

        // vamos assumir identidade também?
        if ($request->loginas) {
            session()->push(config('senhaunica.session_key') . '.undo_loginas', \Auth::user()->codpes);
            \Auth::login($user, true);
            return redirect('/');
        }

        return back();
    }

    /**
     * Mostra os dados de um usuário
     *
     * Utilizado no modal de permissions
     * Inclui informação se usuário é gerenciado pelo env ou não
     */
    public function show($id)
    {
        $this->authorize('admin');

        $user = User::with('permissions', 'roles')->find($id);
        return $user->append('env');
    }

    /**
     * Atualiza as informações de permissão do usuário
     */
    public function update(Request $request, $user_id)
    {
        $this->authorize('admin');

        $request->validate([
            // permissoes hierarquicas
            'level' => 'nullable|in:admin,gerente,user',
            //permissoes da aplicacao
            'permission_app' => 'nullable',
            'role_app' => 'nullable',
        ]);

        $user = User::find($user_id);
        if (!($user instanceof \App\Models\User)) {
            return redirect()->back()->withErrors(['codpes' => $user])->withInput();
        }

        // mantendo permissões de vinculo
        $permissions = $user->permissions->where('guard_name', 'senhaunica')->all();
        // adicionando permissoes de app se existirem
        if ($request->permission_app) {
            foreach ($request->permission_app as $pName) {
                $permissions[] = Permission::where('guard_name', 'app')->where('name', $pName)->first();
            }
        }
        // adicionando permissão hierarquica
        $permissions[] = ($user->env)
        ? $user->permissions->where('guard_name', 'web')->first()
        : Permission::where('name', $request->level)->first();

        $user->syncPermissions($permissions);

        // adicionando roles de app se existirem
        $roles = [];
        if ($request->role_app) {
            foreach ($request->role_app as $pName) {
                $roles[] = Role::where('guard_name', 'app')->where('name', $pName)->first();
            }
        }
        $user->syncRoles($roles);

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
