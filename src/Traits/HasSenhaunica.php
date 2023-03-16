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
    // permissoes hierárquicas
    public static $permissoesHierarquia = [
        'admin',
        'boss',
        'manager',
        'gerente', // == manager, deprecar com o tempo (2/2023)
        'poweruser',
        'user',
    ];

    // permissões por vínculo
    public static $permissoesVinculo = [
        'Servidor',
        'Docente',
        'Estagiario',
        'Alunogr',
        'Alunopos',
        'Alunoceu',
        'Alunoead',
        'Alunopd',
        'Servidorusp',
        'Docenteusp',
        'Estagiariousp',
        'Alunogrusp',
        'Alunoposusp',
        'Alunoceuusp',
        'Alunoeadusp',
        'Alunopdusp',
        'Outros',
    ];

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
     * Acessor: mostra se as permissões do usuário são gerenciadas pelo env
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
     * Retorna a permissão hierárquica do usuário
     *
     * Deveria retornar apenas 1 nome de permission
     * mas pode retornar mais por registro repetido erroneamente
     * Ao alterar o level ele corrige essa condição
     *
     * @return String
     */
    public function getLevelAttribute()
    {
        return $this->permissions->where('guard_name', 'web')->pluck('name')->implode(',');
    }

    /**
     * Retorna a classe do badge da permissão hierárquica do usuário
     *
     * Vermelho para admin, verde para user e amarelo para os demais
     *
     * @param String $level
     * @return String
     */
    public function labelLevel($level = null)
    {
        $level = $level ?: $this->permissions->where('guard_name', 'web')->pluck('name')->implode('');
        switch ($level) {
            case 'admin':return 'danger';
                break;
            case 'user':return 'success';
                break;
            default:return 'warning';
        }
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
        $permissions = (config('senhaunica.dropPermissions'))
        ? []
        : $this->permissions->where('guard_name', 'web')->all();

        $permissions[] = Permission::where('name', $this->env ?? 'user')->first();

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
    public static function listarPermissoesVinculo($vinculos)
    {
        $permissions = [];

        foreach ($vinculos as $vinculo) {
            // vamos colocar o sufixo se for de outra unidade
            $sufixo = ($vinculo['codigoUnidade'] == config('senhaunica.codigoUnidade')) ? '' : 'usp';
            //docente
            if ($vinculo['tipoFuncao'] == 'Docente') {
                $permissions[] = Permission::where('guard_name', 'senhaunica')->where('name', 'Docente' . $sufixo)->first();
                continue;
            }
            //servidor
            if ($vinculo['tipoVinculo'] == 'SERVIDOR' && $vinculo['tipoFuncao'] != 'Docente') {
                $permissions[] = Permission::where('guard_name', 'senhaunica')->where('name', 'Servidor' . $sufixo)->first();
                continue;
            }
            //estagiario
            if ($vinculo['tipoVinculo'] == 'ESTAGIARIORH') {
                $permissions[] = Permission::where('guard_name', 'senhaunica')->where('name', 'Estagiario' . $sufixo)->first();
                continue;
            }
            //Alunopd, Alunogr, Alunopos
            $tipvins = ['ALUNOPD', 'ALUNOGR', 'ALUNOPOS'];
            if (in_array($vinculo['tipoVinculo'], $tipvins)) {
                $permissions[] = Permission::where('guard_name', 'senhaunica')
                    ->where('name', ucfirst($vinculo['tipoVinculo']) . $sufixo)
                    ->first();
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
    public function criarPermissoesPadrao()
    {
        foreach (SELF::$permissoesHierarquia as $permission) {
            Permission::findOrCreate($permission, 'web');
        }
        foreach (SELF::$permissoesVinculo as $permission) {
            Permission::findOrCreate($permission, 'senhaunica');
        }
    }

    /**
     * Cria e retorna usuário na base local ou no replicado
     *
     * Se não conseguiu encontrar/criar o usuário retorna mensagem de erro correspondente.
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

            // atribuindo permissões de vinculo
            $vinculos = array_map(function ($vinculo) {
                $vinculo['codigoUnidade'] = $vinculo['codundclg'];
                $vinculo['tipoFuncao'] = $vinculo['tipvinext'];
                $vinculo['tipoVinculo'] = $vinculo['tipvin'];
                return $vinculo;
            }, Pessoa::listarVinculosAtivos($user->codpes, false));
            $user->syncPermissions(SELF::listarPermissoesVinculo($vinculos));

            // permissao hierarquica
            $user->givePermissionTo(
                Permission::where('guard_name', 'web')->where('name', 'user')->first()
            );
        }

        return $user;
    }

    /**
     * Verifica se o codpes informado é usuário ou está listado no env.
     *
     * @param Int $codpes
     * @return Bool
     */
    public static function verificaUsuarioLocal($codpes)
    {
        return (User::where('codpes', $codpes)->first() ||
            in_array($codpes, config('senhaunica.admins')) ||
            in_array($codpes, config('senhaunica.gerentes')) ||
            in_array($codpes, config('senhaunica.users')))
        ? true
        : false;
    }
}
