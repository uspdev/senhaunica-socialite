<?php

namespace Uspdev\SenhaunicaSocialite\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Uspdev\Replicado\Pessoa;

/**
 * Depende de spatie/laravel-permissions
 */
trait HasSenhaunica
{

    # utilizado para a listagem de usuários e na busca
    public static function getColumns()
    {
        return [
            ['key' => 'codpes', 'text' => 'Nro USP'],
            ['key' => 'name', 'text' => 'Nome'],
            ['key' => 'email', 'text' => 'E-mail'],
        ];
    }

    /**
     * Define se as permissões do usuário são gerenciadas pelo env
     *
     * true = gerenciado pelo env
     *
     * @return Bool|String
     */
    public function getEnvAttribute()
    {
        if (in_array($this->codpes, config('senhaunica.admins'))) {
            return 'admin';
        }
        if (in_array($this->codpes, config('senhaunica.gerentes'))) {
            return 'gerente';
        }
        if (in_array($this->codpes, config('senhaunica.users'))) {
            return 'user';
        }
        return false;

    }

    /**
     * Retorna os nomes das permissões, menos os do guard=web
     *
     * TODO: tem de melhorar aqui
     */
    public function categorias()
    {
        $permissions = $this->getAllPermissions();
        $ret = '';
        foreach ($permissions as $p) {
            if ($p->guard_name == 'web') {
                // $ret .= $p->name . ", ";
            } else {
                $ret .= $p->guard_name . '/' . $p->name . ", ";
            }
        }
        return substr($ret, 0, -2);
    }

    /**
     * Lista toda as permissoes formatadas em html
     *
     * Temporário até criar uma solução melhor
     */
    // public static function listarTodasPermissoes()
    // {
    //     $ret = '';
    //     foreach (Permission::all() as $p) {
    //         $ret .= $p->guard_name . '/' . $p->name . "<br>\n";
    //     }
    //     $gates = array_keys(Gate::abilities());
    //     foreach ($gates as $g) {
    //         $ret .= $g . "<br>\n";
    //     }
    //     return $ret;
    // }

    /**
     * Verifica a existencia do arquivo json referente ao login da senhaunica
     *
     * O arquivo será guardado se senhaunica_debug = true
     */
    public function hasSenhaunicaJson()
    {
        if (Storage::exists('debug/oauth/' . $this->codpes . '.json')) {
            return true;
        }
    }

    /**
     * Seta as permissões para o usuário (permission = true) a partir do oauth
     */
    public function aplicarPermissoes($userSenhaUnica)
    {
        $this->criarPermissoesPadrao();

        $permissions = array_merge(
            $this->listarPermissoesHierarquicas(),
            $this->listarPermissoesApp(),
            $this->listarPermissoesVinculo($userSenhaUnica->vinculo)
        );

        // o sync revoga as permissions não listadas
        $this->syncPermissions($permissions);
    }

    /**
     * Lista as permissões hierarquicas
     */
    public function listarPermissoesHierarquicas()
    {
        $adminPermission = Permission::where('name', 'admin')->first();
        $gerentePermission = Permission::where('name', 'gerente')->first();
        $userPermission = Permission::where('name', 'user')->first();

        if (config('senhaunica.dropPermissions')) {
            $permissions = [];
        } else {
            $permissions = $this->permissions->where('guard_name', 'web')->all();
        }

        if (in_array($this->codpes, config('senhaunica.admins'))) {
            // vamos verificar no config se o usuário é admin
            $permissions[] = $adminPermission;
        } elseif (in_array($this->codpes, config('senhaunica.gerentes'))) {
            // vamos verificar no config se o usuário é gerente
            $permissions[] = $gerentePermission;
        } else {
            // default
            $permissions[] = $userPermission;
        }

        return $permissions;
    }

    /**
     * Lista as permissões de app
     */
    public function listarPermissoesApp()
    {
        return $this->permissions->where('guard_name', 'app')->all();
    }

    /**
     * Lista as permissões referentes aos vínculos da pessoa, extraido do oauth
     *
     * @param Array $vinculos
     */
    public function listarPermissoesVinculo($vinculos)
    {
        $permissions = [];
        foreach ($vinculos as $vinculo) {
            // outra unidade está como outros por enquanto
            if ($vinculo['codigoUnidade'] != config('replicado.codundclg')) {
                $permissions[] = Permission::where('guard_name', 'senhaunica')->where('name', 'Outros')->first();
                continue;
            }
            //docente
            if ($vinculo['tipoFuncao'] == 'Docente') {
                $permissions[] = Permission::where('guard_name', 'senhaunica')->where('name', 'Docente')->first();
                continue;
            }
            //servidor não docente
            if ($vinculo['tipoVinculo'] == 'SERVIDOR' && $vinculo['tipoFuncao'] != 'Docente') {
                $permissions[] = Permission::where('guard_name', 'senhaunica')->where('name', 'Servidor')->first();
                continue;
            }

            //Alunopd
            if ($vinculo['tipoVinculo'] == 'ALUNOPD') {
                $permissions[] = Permission::where('guard_name', 'senhaunica')->where('name', 'Alunopd')->first();
            }
            //Alunogr
            if ($vinculo['tipoVinculo'] == 'ALUNOGR') {
                $permissions[] = Permission::where('guard_name', 'senhaunica')->where('name', 'Alunogr')->first();
            }
            //Alunopos
            if ($vinculo['tipoVinculo'] == 'ALUNOPOS') {
                $permissions[] = Permission::where('guard_name', 'senhaunica')->where('name', 'Alunopos')->first();
            }
        }

        if (empty($permissions)) {
            $permissions[] = Permission::where('guard_name', 'senhaunica')->where('name', 'Outros')->first();
        }

        return $permissions;
    }

    /**
     * Garante que as permissões existam
     */
    public static function criarPermissoesPadrao()
    {
        // hierarquicas
        $permissions = [
            'admin',
            'chief',
            'manager',
            'gerente', // == manager, deprecar com o tempo
            'poweruser',
            'user',
        ];
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // vinculos
        $permissions = [
            'Servidor',
            'Docente',
            'Estagiario',
            'Alunogr',
            'Alunopos',
            'Alunoceu',
            'Alunoead',
            'Alunopd',
            'ServidorUsp',
            'DocenteUsp',
            'EstagiarioUsp',
            'AlunogrUsp',
            'AlunoposUsp',
            'AlunoceuUsp',
            'AlunoeadUsp',
            'AlunopdUsp',
            'Outros',
        ];
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'senhaunica');
        }
    }

    /**
     * Cria e retorna usuário na base local ou no replicado
     *
     * Se nbão conseguiu encontrar/criar o usuário retorna mensagem de erro correspondente.
     *
     * @param $codpes
     * @return User | String
     */
    public static function findOrCreateFromReplicado($codpes)
    {
        $user = User::where('codpes', $codpes)->first();

        if (is_null($user)) {

            if (!hasReplicado()) {
                // se não houver replicado vamos retornar erro
                return 'Usuário não existe na base local';
            }

            $pessoa = Pessoa::dump($codpes);
            if (!$pessoa) {
                // se não encontrou no replicado vamos retornar erro
                return 'Usuário não existe na base da USP';
            }

            $user = new User;
            $user->codpes = $codpes;
            $user->name = $pessoa['nompesttd'];
            $email = Pessoa::email($codpes);
            // nem sempre o email está disponível
            $user->email = empty($email) ? 'semEmail_' . $codpes . '@usp.br' : $email;
            $user->save();
        }
        return $user;
    }

    /**
     * Verifica se o codpes informado é usuário ou está listado no env.
     *
     * @param Int $codpes
     * @return Bool
     */
    public function verificaUsuarioLocal($codpes)
    {
        if (
            User::where('codpes', $codpes)->first() ||
            in_array($codpes, config('senhaunica.admins')) ||
            in_array($codpes, config('senhaunica.gerentes')) ||
            in_array($codpes, config('senhaunica.users'))
        ) {
            return true;
        }
        return false;
    }
}
