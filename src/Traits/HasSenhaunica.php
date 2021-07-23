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
    # utilizado para a listagem de usuários
    public $columns = [
        ['key' => 'codpes', 'text' => 'Nro USP'],
        ['key' => 'name', 'text' => 'Nome'],
        ['key' => 'email', 'text' => 'E-mail'],
    ];

    /**
     * Retorna as permissões para menu do gerenciamento de usuários
     */
    public function getPermissionsToChange()
    {
        $noAdmin = config('senhaunica.dropPermissions') || $this->hasPermissionTo('admin') ? 'disabled' : '';
        $noGerente = config('senhaunica.dropPermissions') || $this->hasPermissionTo('gerente') ? 'disabled' : '';
        $noUser = !$this->hasAnyPermission(['admin', 'gerente']) ? 'disabled' : '';

        return [
            ['value' => 'admin', 'text' => 'Admin', 'disabled' => $noAdmin],
            ['value' => 'gerente', 'text' => 'Gerente', 'disabled' => $noGerente],
            ['value' => 'user', 'text' => 'Usuário', 'disabled' => $noUser],
        ];
    }

    /**
     * Verifica se o usuário consta de admins ou gerentes do env
     */
    public function isManagedByEnv()
    {
        return (in_array($this->codpes, config('senhaunica.admins')) || in_array($this->codpes, config('senhaunica.gerentes')));
    }

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
     * Seta as permissões para o usuário (permission = true)
     */
    public function setDefaultPermission()
    {
        if (config('senhaunica.permission')) {
            // garantindo que as permissions existam
            $permissions = ['admin', 'gerente', 'user'];
            foreach ($permissions as $permission) {
                Permission::findOrCreate($permission);
            }

            // vamos verificar no config se o usuário é admin
            if (in_array($this->codpes, config('senhaunica.admins'))) {
                $this->givePermissionTo('admin');
            } else {
                // vamos revogar o acesso se dropPermissions
                if (config('senhaunica.dropPermissions')) {
                    $this->revokePermissionTo('admin');
                }
            }

            // vamos verificar no config se o usuário é gerente
            if (in_array($this->codpes, config('senhaunica.gerentes'))) {
                $this->givePermissionTo('gerente');
            } else {
                if (config('senhaunica.dropPermissions')) {
                    $this->revokePermissionTo('gerente');
                }
            }

            // default
            $this->givePermissionTo('user');
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
     * Retorna usuário local correspondente ao codpes.
     *
     * Se necessário, cria a partir do replicado somente se listado no .env
     *
     * @param $codpes
     * @return User | Bool
     */
    public static function newLocalUser($codpes)
    {
        if ($user = User::where('codpes', $codpes)->first()) {
            return $user;
        }

        // vamos verificar no config se o usuário está no .env
        if (
            in_array($codpes, config('senhaunica.admins')) ||
            in_array($codpes, config('senhaunica.gerentes')) ||
            in_array($codpes, config('senhaunica.users'))
        ) {
            return SELF::findOrCreateFromReplicado($codpes);
        }

        return false;
    }
}
