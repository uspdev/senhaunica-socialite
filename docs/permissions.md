# Senhaunica-socialite

## Permissões e funções (roles) da aplicação

Inicialmente o senhaunica-socialite permitiu atribuir níveis hierárquicos aos usuários: admin, gerente e user. Com isso, foi possível criar gates baseados nessas permissões para controlar a autorização a determinados recursos. A partir da versão 4.4, novos níveis foram criados ficando: `admin`, `boss`, `manager`, `powruser` e `user`. O gerente ainda está disponível mas deve adequar a aplicação para `manager` pois será removido em versão futura.

Com o objetivo de expandir o gerenciamento das autorizações, a partir da versão 4.4, também foi implementado permissões por vínculo do usuário. Nesse sentido, além das autorizações hierárquicas agora a biblioteca registra permissions correspondentes ao vínculo recebido pelo **oauth**. Então, um servidor não docente receberá a permission `Servidor`, um servidor docente receberá a permission `Docente`, um aluno de graduação recebera `Alunogr` e assim por diante. Caso o docente seja de outra unidade receberá a permission `Docenteusp`. O mesmo é valido para outros vínculos de pessoas de unidades que não a sua.

Ao adicionar um usuário manualmente, as permissões também serão adicionadas caso tenha `uspdev/laravel-replicado` configurado.

[Meet sobre permissions](https://youtu.be/1NMLnMuJP1c)

Outra funcionalidade incuída é o gerenciamento de Permissões e Funções (roles) da aplicação (guard `app`). Na tela de permissões, a atribuição/revogação das permissões é simples - ticando ou não os checkbox correspondentes.

![tela permissões](/docs/permissoes.png)

As permissões são da biblioteca spatie/laravel-permission. Todas as funcionalidades da biblioteca estão disponíveis.

### Exemplo de utilização

Em `App\Providers\AppServiceProvider`, crie as permissões e funções (roles) da aplicação:

```php
public function boot()
{
    // criando algumas permissões a serem utilizadas pela aplicação
    Permission::firstOrCreate(['guard_name' => 'app', 'name' => 'grad']);
    Permission::firstOrCreate(['guard_name' => 'app', 'name' => 'posgrad']);
    Permission::firstOrCreate(['guard_name' => 'app', 'name' => 'academica']);
    Permission::firstOrCreate(['guard_name' => 'app', 'name' => 'financeira']);
    Permission::firstOrCreate(['guard_name' => 'app', 'name' => 'administrativa']);

    // criando role e tribuindo permissões a ela
    $role = Role::firstOrCreate(['guard_name' => 'app', 'name' => 'diretoria']);
    $role->givePermissionTo(['academica', 'financeira', 'administrativa']);
}

```

Em `App\Providers\AuthServiceProvider`, crie os gates a serem utilizados na aplicação. Não é necessário criar gates para as Funções (roles).

```php
public function boot()
{
    Gate::define('graduacao', function ($user) {
        return $user->hasPermissionTo('graduacao', 'app');
    });
    Gate::define('posgraduacao', function ($user) {
        return $user->hasPermissionTo('posgraduacao', 'app');
    });
    Gate::define('academica', function ($user) {
        return $user->hasPermissionTo('academica', 'app');
    });
    Gate::define('financeira', function ($user) {
        return $user->hasPermissionTo('financeira', 'app');
    });
    Gate::define('administrativa', function ($user) {
        return $user->hasPermissionTo('administrativa', 'app');
    });
}
```

Utilize os gates na sua aplicação, como qualquer outro gate.

Na interface de usuários do `uspdev/senhaunica-socialite` será possível atribuir cada permissão ou função aos usuários.

[Voltar para README.md](../README.md)
