<?php

namespace Uspdev\SenhaunicaSocialite\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Uspdev\Replicado\Pessoa;
use \Uspdev\Replicado\Posgraduacao;
use \Uspdev\Replicado\Graduacao;

/**
 * Depende de spatie/laravel-permissions
 */
trait HasSenhaunica
{
    use \Kyslik\ColumnSortable\Sortable;

    /** nome do guard de app.
     * Na verdade devemos usar o guard padrão que é web
     * dessa forma os gates devem funcionar automaticamente
     * @var String
     */
    public static $appNs = 'web';

    /**
     * Nome do guard a ser utilizado nas permissões hierárquicas e de vínculo
     */
    public static $hierarquiaNs = 'senhaunica';
    public static $vinculoNs = 'senhaunica';

    /**
     * Todas as permissoes hierárquicas, do maior para o menor
     */
    public static $permissoesHierarquia = [
        'admin',
        'boss',
        'manager',
        // 'gerente', // == manager, removido (2/2023)
        'poweruser',
        'user',
    ];

    /**
     * Todas as permissoes de vínculo
     * veja o método listarPermissoesVinculo() para alterar
     */
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
            return 'manager';
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
        return $this->permissions
            ->where('guard_name', self::$hierarquiaNs)
            ->whereIn('name', self::$permissoesHierarquia)
            ->pluck('name')->implode(',');
    }

    /**
     * Retorna a classe do badge da permissão hierárquica do usuário
     *
     * Ou retorna a classe do badge do $level informado
     * Vermelho para admin, verde para user e amarelo para os demais
     *
     * @param String $level
     * @return String
     */
    public function labelLevel($level = null)
    {
        $level = $level ?: $this->permissions->where('guard_name', Self::$hierarquiaNs)
            ->whereIn('name', Self::$permissoesHierarquia)
            ->pluck('name')->implode('');

        switch ($level) {
            case 'admin':
                return 'danger';
                break;
            case 'user':
                return 'success';
                break;
            default:
                return 'warning';
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
     *
     * @param Array $userSenhaUnica Usuário retornado do oauth
     */
    public function aplicarPermissoes($userSenhaUnica)
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->criarPermissoesPadrao();

        $permissions = array_merge(
            $this->listarPermissoesHierarquicas(),
            $this->listarPermissoesApp(),
            $this->listarPermissoesVinculo($userSenhaUnica->vinculo, $userSenhaUnica->codpes)
        );

        // o sync revoga as permissions não listadas
        $this->syncPermissions($permissions);
    }

    /**
     * Lista as permissões hierarquicas
     *
     * A logica aqui está confusa. Precisa melhorar
     */
    public function listarPermissoesHierarquicas()
    {
        $permissions = (config('senhaunica.dropPermissions'))
        ? []
        : $this->permissions->where('guard_name', User::$hierarquiaNs)->all();

        // se estiver no env vai sobrescrever a existente
        if ($this->env) {
            $permissions = [];
            $permissions[] = Permission::where('name', $this->env)->first();
        }

        // se não tiver nada, vamos retornar a permissão user
        $permissions[] = $permissions ?: Permission::where('name', 'user')->first();

        return $permissions;
    }

    /**
     * Lista as permissões de app
     */
    public function listarPermissoesApp()
    {
        return $this->permissions->where('guard_name', User::$appNs)->all();
    }

    /**
     * Lista as permissões referentes aos vínculos da pessoa, extraido do oauth
     *
     * @param Array $vinculos
     */
    public static function listarPermissoesVinculo($vinculos, $codpes = null)
    {
        $permissions = [];

        foreach ($vinculos as $vinculo) {

            if(is_null($codpes)) $codpes = $vinculo['codpes'];

            if (isset($vinculo['tipvinext']) && $vinculo['tipvinext'] == 'Servidor Designado') continue;

            // vamos colocar o sufixo se for de outra unidade
            $sufixo = ($vinculo['codigoUnidade'] == config('senhaunica.codigoUnidade')) ? '' : 'usp';
            //docente
            if ($vinculo['tipoFuncao'] == 'Docente') {
                $permissions[] = Permission::where('guard_name', self::$vinculoNs)
                    ->where('name', 'Docente' . $sufixo)->first();
                $permissions = array_merge($permissions, self::listarPermissionVinculoSetor($vinculo, 'Docente' . $sufixo));
                continue;
            }
            //servidor
            if ($vinculo['tipoVinculo'] == 'SERVIDOR' && $vinculo['tipoFuncao'] != 'Docente') {
                $permissions[] = Permission::where('guard_name', self::$vinculoNs)
                    ->where('name', 'Servidor' . $sufixo)->first();
                $permissions = array_merge($permissions, self::listarPermissionVinculoSetor($vinculo, 'Servidor' . $sufixo));
                continue;
            }
            //estagiario
            if ($vinculo['tipoVinculo'] == 'ESTAGIARIORH') {
                $permissions[] = Permission::where('guard_name', self::$vinculoNs)
                    ->where('name', 'Estagiario' . $sufixo)->first();
                $permissions = array_merge($permissions, self::listarPermissionVinculoSetor($vinculo, 'Estagiario' . $sufixo));
                continue;
            }
            //Alunopd, Alunoceu, Alunoead, Alunoconvenioint
            $tipvins = ['ALUNOPD', 'ALUNOCEU', 'ALUNOEAD', 'ALUNOCONVENIOINT'];
            if (in_array($vinculo['tipoVinculo'], $tipvins)) {
                $permissionName = ucfirst(strtolower($vinculo['tipoVinculo'])) . $sufixo;
                $permissions[] = Permission::where('guard_name', self::$vinculoNs)
                    ->where('name', $permissionName)
                    ->first();
            }

            //Alunogr
            if ($vinculo['tipoVinculo'] == 'ALUNOGR') {

                $permissionName = ucfirst(strtolower($vinculo['tipoVinculo'])) . $sufixo;
                $permissions[] = Permission::where('guard_name', self::$vinculoNs)
                    ->where('name', $permissionName)
                    ->first();

                if (hasReplicado()) {
                    $vinculoAtivo = Graduacao::obterCursoAtivo($codpes);
                    if($vinculoAtivo) {
                        $permissions[] = Permission::findOrCreate('Alunogr' . '.' . $vinculoAtivo['codcur'], self::$vinculoNs);
                    }
                }
            }

            //ALUNOPOS
            if ($vinculo['tipoVinculo'] == 'ALUNOPOS') {

                $permissionName = ucfirst(strtolower($vinculo['tipoVinculo'])) . $sufixo;
                $permissions[] = Permission::where('guard_name', self::$vinculoNs)
                    ->where('name', $permissionName)
                    ->first();

                if (hasReplicado()) {
                    $vinculoAtivo = Posgraduacao::obterVinculoAtivo($codpes);
                    if($vinculoAtivo) {
                        $permissions[] = Permission::findOrCreate('Alunopos' . '.' . $vinculoAtivo['codare'], self::$vinculoNs);
                    }
                }
            }
        }

        if (empty($permissions)) {
            // se nao associou nenhuma permission, vamos atribuir "Outros"
            $permissions[] = Permission::where('guard_name', self::$vinculoNs)
                ->where('name', 'Outros')->first();
        }

        return $permissions;
    }

    /**
     * Retorna as permissões relacionadas ao setor para pessoas ligadas à unidade
     *
     * @param $vinculo Vinculo obtido do array de vinculos do oauth
     * @param $nomeVinculo Nome do vinculo conforme $permissoesVinculo
     * @return Array
     */
    public static function listarPermissionVinculoSetor($vinculo, $nomePermissionVinculo)
    {
        if (str_contains($nomePermissionVinculo, 'usp')) {
            return [];
        }
        $permissions = [];
        // se senhaunica é nomeAbreviadoSetor, se replicado é nomabvset
        if (isset($vinculo['nomeAbreviadoSetor']) || isset($vinculo['nomabvset'])) {
            $setor = isset($vinculo['nomeAbreviadoSetor']) ? $vinculo['nomeAbreviadoSetor'] : $vinculo['nomabvset'];
            $setor = strtolower(explode('-', $setor)[0]); // tira a parte numérica
            $permissions[] = Permission::findOrCreate($nomePermissionVinculo . '.' . $setor, self::$vinculoNs);
        }
        return $permissions;
    }

    /**
     * Garante que as permissões existam
     */
    public function criarPermissoesPadrao()
    {
        foreach (SELF::$permissoesHierarquia as $permission) {
            Permission::findOrCreate($permission, self::$hierarquiaNs);
        }
        foreach (SELF::$permissoesVinculo as $permission) {
            Permission::findOrCreate($permission, self::$vinculoNs);
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
                Permission::where('guard_name', User::$hierarquiaNs)->where('name', 'user')->first()
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
