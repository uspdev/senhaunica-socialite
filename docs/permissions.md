# Senhaunica-socialite

## Permissões e funções (roles)

### Permissões da biblioteca

Inicialmente o `senhaunica-socialite` permitiu atribuir níveis hierárquicos aos usuários: admin, gerente e user. Com isso, foi possível criar gates baseados nessas permissões para controlar a autorização a determinados recursos. A partir da versão 4.4, novos níveis foram criados ficando: `admin`, `boss`, `manager`, `poweruser` e `user`. O gerente foi removido e você deve adequar a aplicação para `manager`.

Com o objetivo de expandir o gerenciamento das autorizações, a partir da versão 4.4, também foi implementado permissões por vínculo do usuário. Nesse sentido, além das autorizações hierárquicas agora a biblioteca registra permissions correspondentes ao vínculo recebido pelo **oauth**. Então, um servidor não docente receberá a permission `Servidor`, um servidor docente receberá a permission `Docente`, um aluno de graduação receberá `Alunogr` e assim por diante. Caso o docente seja de outra unidade receberá a permission `Docenteusp`. O mesmo é valido para outros vínculos de pessoas de unidades que não a sua.

Para verificar um vínculo, a biblioteca cria gates `senhaunica.servidor`, `senhaunica.docente`, e assim por diante. Para utilizar na aplicação pode-se:

    @can('senhaunica.servidor')
    ...
    @endcan

Todas as permissões da biblioteca `uspdev/senhaunica-socialite` estão no namespace/guard `senhaunica`.

Ao adicionar um usuário manualmente, as permissões correspondentes também serão adicionadas, caso tenha `uspdev/laravel-replicado` configurado.

[Meet sobre permissions](https://youtu.be/1NMLnMuJP1c)

### Permissões da aplicação

Outra funcionalidade incluída é o gerenciamento de Permissões e Funções (roles) da aplicação (guard `web`). Na tela de permissões, a atribuição/revogação das permissões é simples - ticando ou não os checkbox correspondentes.

![tela permissões](/docs/permissoes.png)

As permissões são da biblioteca spatie/laravel-permission. Todas as funcionalidades da biblioteca estão disponíveis para uso.

### Exemplo de utilização

Para criar as permissões e funções (roles) da aplicação é necessário realizar uma migration para que estas sejam criadas e persistam no banco de dados.

Para criar a migration use:

    php artisan make:migration seed_permission_table

No método `up()` do arquivo criado coloque as suas permissions:

```php

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
...

public function up()
{
    // criando algumas permissões a serem utilizadas pela aplicação
    Permission::firstOrCreate(['name' => 'grad']);
    Permission::firstOrCreate(['name' => 'posgrad']);
    Permission::firstOrCreate(['name' => 'academica']);
    Permission::firstOrCreate(['name' => 'financeira']);
    Permission::firstOrCreate(['name' => 'administrativa']);

    // criando role e tribuindo permissões a ela
    $role = Role::firstOrCreate(['name' => 'diretoria']);
    $role->givePermissionTo(['academica', 'financeira', 'administrativa']);
}
```

E finalmente aplique a migration

    php artisan migrate

Os gates serão criados automaticamente pela biblioteca `spatie/laravel-permission`.

Utilize os gates na sua aplicação, como qualquer outro gate ou via permission:

```php
@can('graduacao')
  ...
@endcan
@if(Auth::user() && Auth::user()->hasPermissionTo('graduacao'))
  ...
@endif
```

Na interface de usuários do `uspdev/senhaunica-socialite` será possível atribuir cada permissão ou função aos usuários.


[Voltar para README.md](../README.md)
