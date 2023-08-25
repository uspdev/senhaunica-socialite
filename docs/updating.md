## Atualizando à partida da versão 2


A atualização para versão 4 exije alguns ajustes no código.

Primeiramente atualize o `composer.json` para usar a nova versão e rode `composer update`.

    "uspdev/senhaunica-socialite": "^4.0"

Deve-se desfazer/verificar **pelo menos** os seguintes arquivos:

- `app/Providers/EventServiceProvider.php`, remover as linhas que chamam o SenhaunicaSocialite
- `config/services.php`, remover a seção senhaunica

Por padrão a versão 4 incorpora autorização e rotas/controller internos. Se for conveniente, esses recursos podem ser desabilitados por meio do `config/senhaunica.php`. Se optar por utilizar esses recursos, verifique/ajuste os seguintes arquivos:

- `routes/web.php`, remover as rotas login, callback e logout
- `App/Http/Controllers/Auth/LoginController.php`, apagar o arquivo
- `App/Providers/AuthServiceProvider.php`, remover gates `admin` e `user`

A tabela `users` deve possuir a coluna `codpes`. Se for o caso, publique a migration e ajuste o arquivo publicado conforme sua necessidade.

Para usar a autorização, é necessário:

- Incluir as traits no model do user

        use \Spatie\Permission\Traits\HasRoles;
        use \Uspdev\SenhaunicaSocialite\Traits\HasSenhaunica;

- publicar e migrar as tabelas correspondentes:

        php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
        php artisan migrate

Confira o `.env` e o `.env.example` se estão de acordo com as recomendações atuais.

> OBS.: a variável `ADMINS` foi renomeada para `SENHAUNICA_ADMINS`.
