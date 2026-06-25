<?php

return [

    'default' => env('BROADCAST_CONNECTION', 'null'),

    'connections' => [

        'reverb' => [
            'driver' => 'reverb',
            'key'    => env('REVERB_APP_KEY'),
            'secret' => env('REVERB_APP_SECRET'),
            'app_id' => env('REVERB_APP_ID'),
            'options' => [
                'host'   => env('REVERB_HOST'),
                'port'   => env('REVERB_PORT', 8080),    // ← was 443, fix to 8080
                'scheme' => env('REVERB_SCHEME', 'http'), // ← was https
                'useTLS' => env('REVERB_SCHEME', 'http') === 'https',
            ],
            'client_options' => [],
        ],

        'log'  => ['driver' => 'log'],
        'null' => ['driver' => 'null'],

    ],

];