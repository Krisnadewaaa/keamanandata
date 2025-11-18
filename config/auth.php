<?php

return [

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'pembeli' => [
            'driver' => 'session',
            'provider' => 'pembelis',
        ],

        'penitip' => [
            'driver' => 'session',
            'provider' => 'penitips',
        ],

        'pegawai' => [
            'driver' => 'session',
            'provider' => 'pegawais',
        ],

        'organisasi' => [
            'driver' => 'session',
            'provider' => 'organisasis',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],

        'pembelis' => [
            'driver' => 'eloquent',
            'model' => App\Models\Pembeli::class,
        ],

        'penitips' => [
            'driver' => 'eloquent',
            'model' => App\Models\Penitip::class,
        ],

        'pegawais' => [
            'driver' => 'eloquent',
            'model' => App\Models\Pegawai::class,
        ],

        'organisasis' => [
            'driver' => 'eloquent',
            'model' => App\Models\Organisasi::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        'pembelis' => [
            'provider' => 'pembelis',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        'penitips' => [
            'provider' => 'penitips',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        'pegawais' => [
            'provider' => 'pegawais',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        'organisasis' => [
            'provider' => 'organisasis',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

];
