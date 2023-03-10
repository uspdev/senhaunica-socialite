<?php

return [

    // Vamos criar o guard a ser utilizado pelo senhaunica
    'senhaunica' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    // Vamos garantir o guard a ser utilizado pela aplicação
    'app' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
];
