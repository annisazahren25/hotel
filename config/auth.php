<?php

return [

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'guests',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'guests',
        ],
    ],

    'providers' => [
        'guests' => [
            'driver' => 'eloquent',
            'model' => App\Models\Guest::class,
        ],
    ],

    'passwords' => [
        'guests' => [
            'provider' => 'guests',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,

];