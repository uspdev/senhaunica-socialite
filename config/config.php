<?php

return [
    // para rotas internas
    'routes' => true, // usa rotas e controller internos

    // coloque um prefixo em caso de colisão de rotas
    // para todas as rotas internas da biblioteca (login, loginas, callback, logout e users).
    'prefix' => '',

    'middleware' => ['web'], // you probably want to include 'web' here

    // chave da sessão. Troque em caso de colisão com outra variável de sessão
    'session_key' => 'senhaunica-socialite',

    // -----------------------------------------------------
    // Para views internas da biblioteca (users)

    // template a ser estendido. Deve possuir a section "content"
    // usualmente a aplicação possui layouts.app. Se não possui considere criar.
    'template' => 'laravel-usp-theme::master',

    // define as rotas para o gerenciador de usuários interno, dentro de prefix
    // se vazio, desabilita a rota de gerenciamento de usuários interna
    'userRoutes' => 'senhaunica-users',

    // se true, habilita botão para remover usuário (destroy)
    'destroyUser' => true,

    // view para editar campo de usuário personalizado. Pode ser mais de uma coluna
    // 'key' é opcional e se estiver setado permite ordenar por esta coluna, passando o nome da coluna do DB
    // é passado $user para a view
    'customUserField' => [
        // ['view' => 'users.partials.include', 'key' => 'db_column', 'label' => 'custom label', 'width' => '100px'],
    ],

    // Define o gate para a rota de busca de pessoas
    'findUsersGate' => 'admin',

    // fim views internas
    // -----------------------------------------------------

    // usa as permissoes internas, padrão para v4.
    // Se false, não usará permission ao efetuar login
    'permission' => true,

    // permite login somente de usuários já cadastrados na base local ou autorizados nos admins, gerentes ou users
    'onlyLocalUsers' => false,

    // se true, revoga as permissões do usuario se não estiver no env.
    // quer dizer que as permissões serão gerenciadas todas a partir do env da aplicação.
    // relevante se permission=true
    'dropPermissions' => env('SENHAUNICA_DROP_PERMISSIONS', false),

    // cadastre os admins separados por virgula
    // relevante se permission=true
    'admins' => array_map('trim', explode(',', env('SENHAUNICA_ADMINS', ''))),

    // cadastre os gerentes separados por virgula.
    // relevante se permission=true
    // Apesar do gate chamar manager, no env ainda mantemos gerente
    'gerentes' => array_map('trim', explode(',', env('SENHAUNICA_GERENTES', ''))),

    // se quiser cadastre os usuários comuns autorizados. Relevante se onlyLocalUsers = true
    // relevante se permission=true
    'users' => array_map('trim', explode(',', env('SENHAUNICA_USERS', ''))),

    // se true, ele grava no filesystem o retorno json do servidor oauth
    'debug' => (bool) env('SENHAUNICA_DEBUG', true),

    'dev' => env('SENHAUNICA_DEV', 'no'),
    'callback_id' => env('SENHAUNICA_CALLBACK_ID'),

    // codigo da unidade para identificar logins proprios
    // relevante se permission=true
    'codigoUnidade' => env('SENHAUNICA_CODIGO_UNIDADE'),

    // SENHAUNICA_KEY e SENHAUNICA_SECRET são carregados em services.php da biblioteca
];
