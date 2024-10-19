## Provider para utilização de senha única USP no Laravel

Biblioteca que permite integrar sua aplicação laravel com a autenticação centralizada da USP utilizando a senha única.

Como funcionalidades adicionais, além da comunicação com o servidor de autenticação, ele também fornece:

- as rotas e controllers necessários para efetuar o login e logout da aplicação;
- um sistema de autorização em cinco níveis (permission) para a aplicação;
- uma rota `/loginas` quer permite assumir identidade de outra pessoa;
- uma interface de gerenciamento de usuários da aplicação, permitindo incluir, remover e atribuir permissões;

> OBS.: Os recursos adicionais podem ser desativados caso não deseje utilizar.

Vídeos sobre a utilização desta biblioteca:

- [1.x](https://youtu.be/jLFM2AUFJgw)
- [2.x](https://www.youtube.com/watch?v=t6Zf3nK-oIo)
- [3.x] ...
- [4.x] ...

Dependências em PHP, além das default do laravel:

php-curl

### Histórico

#### 3/2023: versão 4.4
* gerenciamento de permissões da aplicação no guard `web` (padrão)
* mudando padrão do debug para `true`.
* criação de permissões de vínculo dos usuários no namespace `senhaunica`
* (https://youtu.be/1NMLnMuJP1c)
* Necessário atualizar `.env`/`.env.example` e ajustar `config` se publicado

### Instalação

#### Declarar a `trait` do model `User`

Antes da instalação, da biblioteca, em `App/Models/User.php`, dentro da classe `User` incluir as seguintes linhas:

```php
class User extends Authenticatable
{
use \Spatie\Permission\Traits\HasRoles;
use \Uspdev\SenhaunicaSocialite\Traits\HasSenhaunica;
...
```
Proceda com a instalação:

```
composer require uspdev/senhaunica-socialite
```

### Configuração básica - nova instalação

#### Publique e rode as migrations

As migrations modificam a tabela `users` e criam as tabelas de autorização.

```
php artisan vendor:publish --provider="Uspdev\SenhaunicaSocialite\SenhaunicaServiceProvider" --tag="migrations"
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```
#### Cadastre o `callback_id`

A url é o que está cadastrado no `APP_URL` mais `/callback`, exemplo: `http://localhost:8000/callback`

- dev: https://dev.uspdigital.usp.br/adminws/oauthConsumidorAcessar
- prod: https://uspdigital.usp.br/adminws/oauthConsumidorAcessar

#### Coloque variáveis no `.env` e `.env.example` da sua aplicação

```sh
# SENHAUNICA-SOCIALITE ######################################
# https://github.com/uspdev/senhaunica-socialite

# Credenciais/informações do oauth
SENHAUNICA_KEY=fflch_sti
SENHAUNICA_SECRET=sua_super_chave_segura
SENHAUNICA_CALLBACK_ID=85

# URL do servidor oauth no ambiente de dev (default=no)
#SENHAUNICA_DEV="https://dev.uspdigital.usp.br/wsusuario/oauth"

# URL do servidor oauth para uso com senhaunica-faker
#SENHAUNICA_DEV="http://127.0.0.1:3141/wsusuario/oauth"

# Desabilite para não salvar o retorno do oauth em storage/app/debug/oauth/ (default=true)
#SENHAUNICA_DEBUG=

# AS CONFIGURAÇÕES A SEGUIR são relevantes se permission=true

# Esses usuários terão privilégios especiais
#SENHAUNICA_ADMINS=11111,22222,33333
#SENHAUNICA_GERENTES=4444,5555,6666

# Se os logins forem limitados a usuários cadastrados (onlyLocalUsers=true),
# pode ser útil cadastrá-los aqui.
#SENHAUNICA_USERS=777,888

# Se true, os privilégios especiais serão revogados ao remover da lista (default=false)
#SENHAUNICA_DROP_PERMISSIONS=

# Código da unidade para identificar os logins próprios ou de outras unidades
SENHAUNICA_CODIGO_UNIDADE=

```

### [Permissões e funções da aplicação](docs/permissions.md)
### [Atualizando à partir da versão 2](docs/updating.md)
### [Arquivo de configuração](docs/configuracao_detalhes.md)
### [Informações para desenvolvedores](docs/desenvolvedores.md)
