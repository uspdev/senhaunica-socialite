<?php

return [
    // para rotas internas
    'routes' => true, // usa rotas e controller internos
    'prefix' => '', // coloque um prefixo em caso de colisão de rotas (login, callback e logout).
    'middleware' => ['web'], // you probably want to include 'web' here

    // usa as permissoes internas, padrão para v3.
    // Se false não usará permission ao efetuar login
    'permission' => true,

    // se true, revoga as permissões do usuario se não estiver no env
    'dropPermissions' => (bool) env('SENHAUNICA_DROP_PERMISSIONS', false),

    // cadastre os admins separados por virgula
    'admins' => array_map('trim', explode(',', env('SENHAUNICA_ADMINS', ''))),

    // cadastre os gerentes separados por virgula
    'gerentes' => array_map('trim', explode(',', env('SENHAUNICA_GERENTES', ''))),

    'dev' => env('SENHAUNICA_DEV', 'no'),
    'debug' => (bool) env('SENHAUNICA_DEBUG', false),
    'callback_id' => env('SENHAUNICA_CALLBACK_ID'),
];
