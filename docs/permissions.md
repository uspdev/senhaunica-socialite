# Senhaunica-socialite

## Permissões e funções (roles)

### Permissões da biblioteca

#### Permissões hierárquicas

Inicialmente o `senhaunica-socialite` permitiu atribuir níveis hierárquicos aos usuários: admin, gerente e user. Com isso, foi possível criar gates baseados nessas permissões para controlar a autorização a determinados recursos. A partir da versão 4.4, novos níveis foram criados ficando: `admin`, `boss`, `manager`, `poweruser` e `user`. O gerente foi removido e você deve adequar a aplicação para `manager`.

#### Permissões de Vínculo

Com o objetivo de expandir o gerenciamento das autorizações, a partir da versão 4.4, também foi implementado permissões por **vínculo do usuário** (recebido do OAuth da USP). Nesse sentido, além das autorizações hierárquicas, agora a biblioteca registra permissions correspondentes ao vínculo de cada pessoa.

**Exemplos de vínculos:**

- Um servidor não docente receberá a permission `Servidor`
- Um servidor docente receberá a permission `Docente`
- Um aluno de graduação receberá `Alunogr`
- E assim por diante...

**Diferenciação por unidade:**

- Se a pessoa é da mesma unidade da aplicação: recebe o vínculo normal (ex: `Docente`)
- Se a pessoa é de outra unidade: recebe o vínculo com sufixo `usp` (ex: `Docenteusp`)

Para verificar um vínculo, a biblioteca cria gates `senhaunica.servidor`, `senhaunica.docente`, e assim por diante. Para utilizar na aplicação pode-se:

    @can('senhaunica.servidor')
    ...
    @endcan

Todas as permissões da biblioteca `uspdev/senhaunica-socialite` estão no namespace/guard `senhaunica`.

Ao adicionar um usuário manualmente, as permissões correspondentes também serão adicionadas, caso tenha `uspdev/laravel-replicado` configurado.

[Meet sobre permissions](https://youtu.be/1NMLnMuJP1c)

### Permissões da aplicação

Além das permissões automáticas do OAuth (guard `senhaunica`), a biblioteca também oferece gerenciamento de **Permissões e Funções (roles) customizadas da sua aplicação** (guard `web`).

**Diferença:**

- **Guard `senhaunica`**: Permissões vêm automaticamente do OAuth (hierarquia + vínculo)
- **Guard `web`**: Permissões que você cria manualmente para sua aplicação

#### Atribuindo permissions ao usuário

Na interface de usuários do `uspdev/senhaunica-socialite` (rota `/senhaunica-users`), será possível atribuir cada permissão ou função aos usuários através dos checkboxes.

![tela permissões](/docs/permissoes.png)

As permissões são da biblioteca spatie/laravel-permission. Todas as funcionalidades da biblioteca estão disponíveis para uso.

#### Exemplo de utilização

Para criar as permissões e funções (roles) **customizadas da sua aplicação** é necessário realizar uma migration para que estas sejam criadas e persistam no banco de dados. Essas serão criadas no guard `web`.

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

#### Gates customizados da aplicação

Além das permissions automáticas e customizadas, a aplicação pode definir gates específicos para regras de negócio.

Exemplo:

```php
Gate::define('acesso_academico', function ($user) {
    return $user->can('admin')
        || $user->hasAnyRole(['graduacao', 'posgrad'])
        || $user->can('senhaunica.docente');
});
```

##### Usando no Blade (exemplos)

**Permissões da biblioteca (guard `senhaunica`):**

```blade
@can('manager')
  {{-- Manager ou acima (admin, boss, manager) --}}
@endcan

@can('senhaunica.docente')
  {{-- Verificar se é docente --}}
@endcan
```

**Permissões da aplicação (guard `web`):**

```blade
@can('academica')
  {{-- Verificar permission customizada --}}
@endcan

@role('diretoria')
  {{-- Verificar se tem role customizada --}}
@endrole
```

##### Verificando no controller

Utilize os gates na sua aplicação, como qualquer outro gate ou via permission:

```php
// Verificar hierarquia (guard senhaunica)
$this->authorize('manager');

// Verificar permissão customizada (guard web)
if (Auth::user() && Auth::user()->hasPermissionTo('academica')) {
    // usuário tem a permissão
}

// Ou de forma mais simples
@can('academica')
  ...
@endcan
```

[Voltar para README.md](../README.md)
