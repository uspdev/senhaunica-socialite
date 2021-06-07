<?php

return [
    // para rotas
    'prefix' => '', // coloque um prefixo em caso de colisão de rotas (login, callback e logout).
    'middleware' => ['web'], // you probably want to include 'web' here

    'dev' => env('SENHAUNICA_DEV', 'no'),
    'admins' => env('SENHAUNICA_ADMINS'),
    'debug' => (bool) env('SENHAUNICA_DEBUG', false),
    'callback_id' => env('SENHAUNICA_CALLBACK_ID'),
];
