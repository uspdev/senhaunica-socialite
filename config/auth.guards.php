<?php

return [

    // Vamos criar o guard a ser utilizado pelo senhaunica
    'senhaunica' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
];
