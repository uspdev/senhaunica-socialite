# Senhaunica-socialite

## Arquivo de configuração

Caso você queira modificar o comportamento padrão de algumas partes como por exemplo, desabilitar a autorização ou as rotas internas, publique o arquivo de configuração e ajuste conforme necessário. A publicação é necessária somente se for alterar alguma configuração.

```sh
php artisan vendor:publish --provider="Uspdev\SenhaunicaSocialite\SenhaunicaServiceProvider" --tag="config" --force
```

- **permission** (padrão true)

  As permissões internas de três níveis serão utilizadas por meio da biblioteca `spatie-permissions`. Com isso, os gates `admin`, `manager` e `user` estarão disponíveis.
  
  A partir da versão 4.4 foram acrecentadas mais 2 permissões hierárquicas, ficando então: `admin`, `boss`, `manager`, `poweruser` e `user`.
  
  Atenção: O gate `gerente` foi alterado para `manager`.

- **onlyLocalUsers** (padrão false)

  Por padrão, qualquer usuário com senha única poderá fazer login. Mudando para true, a biblioteca permitirá somente o login de pessoas que já estão na base de dados local ou que estejam na lista de codpes do `.env`.

- **destroyUser** (padrão false)

  Se true a interface interna de gerenciamento permitirá a remoção de usuários da base local. Use com cuidado caso outras tabelas dependa/tenham relacionamento com `users`.

- **debug** (padrão `true`)

  Se true, ele grava no filesystem o retorno json do servidor oauth. O json pode ser viasualizado na interface de suuários da biblioteca.


#### Rotas e controllers

Essa biblioteca possui rotas internas para **login**, **logout**, **users** e **loginas** e o respectivo controller fornecendo uma solução pronta para muitas aplicações.

Caso sua aplicação necessite de um processo mais complexo, você pode desabilitar com `routes=false`. Nesse caso, não é necessário usar a migration que modifica a tabela users.

Mas você deve implementar sua solução de rotas e controller para gerenciar os logins e logouts usando senha única ou não.

#### Menu na aplicação

No `config/laravel-usp-theme.php`, coloque ou reposicione a chave `senhaunica-socialite` para mostrar o menu. Ele será visível somente para `admin`.

    [
        'key' => 'senhaunica-socialite',
    ],

## Gerenciamento de Usuários

A biblioteca possui um painel de gerenciamento de usuários. A rota padrão é `/senhaunica-users` mas pode ser modificado nas configurações.

Essa interface permite adicionar e remover usuários, ajustar as permissões, dentre outras facilidades. Ela é autorizada somente para usuários `admin`.

A partir da **versão 4.2**, é possivel adicionar uma coluna personalizada. Veja a documentação sobre [customUserField](docs/customUserField.md).

A partir da **versão 4.3** está disponível componente select para procurar pessoas. Veja documentação sobre [componentes](docs/componentes.md).

A partir da **versão 4.4** está disponível o gerenciamento avançado das permissões.

#### Autorização

Se você desabilitar as permissões `permission=false` não é necessário usar a migration do `spatie/laravel-permission`.

Os recursos de permissões hierárquicas, permissões de vínculo e permissões da aplicação estarão indisponíveis.

- **dropPermissions** (padrão `false`) (`env`)

  Se `true`, revoga as permissões do usuario se não estiver no env. Quer dizer que as permissões serão gerenciadas todas a partir do env da aplicação.

  - **codigoUnidade** (padrão igual a `REPLICADO_CODUNDCLG` via `env`)

  Assim como no replicado é necessário configurar o cóidgo da unidade para diferenciar usuários locais dos demais usuários. Um aluno de graduação da unidade local possuirá vinculo `Alunogr` enquanto alunos de graduação de outras unidades possuirão vinculo `Alunogrusp`. Não é possivel diferenciar a unidade do aluno se for de outra unidade.

## Configuração da biblioteca laravel-permission

A biblioteca [laravel-permission](https://github.com/spatie/laravel-permission/) vem habilitada por padrão. Ela é poderosa, flexível e bem estabelecida pela comunidade laravel no quesito Funções (roles) e Permissões.

Os números USP inseridos em SENHAUNICA_ADMINS e SENHAUNICA_GERENTES recebem as permissões **admin** e **gerente** respectivamente. Todos os usuários por padrão recebem a permissão **user**. Essas permissões são criadas no guard `web` e associadas automaticamente a `Gates`, assim não é necessário definí-las em `App\Providers\AuthServiceProvider`.

OBS.: Os **admins** são SUPER-ADMINS, ou seja eles possuem acesso em todos os gates.

Neste momento você tem um poder enorme de regras de permissionamento no seu sistema, podendo criar outras _permissions_, agrupá-las em _roles_ ou mesmo listar as permissões de um usuários, como:

    $user->getPermissionNames();

Ou listar todos usuários com uma dada permissão:

    $users = User::permission('admin')->get();

Como as permissões são gates, eles podem ser usados diretamente no blade com a diretiva `@can` ou em qualquer parte do sistema da forma usual.

### Gerenciamento das Permissões dos Usuários

Por padrão, a rota /users estará disponível para os admins. Nela é possível gerenciar as permissões dos usuários, incluindo gerentes e admins, desde que não estejam no `.env`.
