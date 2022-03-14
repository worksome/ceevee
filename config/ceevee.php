<?php

declare(strict_types=1);

return [

    'default' => env('CEEVEE_DRIVER', 'null'),

    'services' => [

        'sovren' => [
            'account_id' => env('SOVREN_ACCOUNT_ID'),
            'service_key' => env('SOVREN_SERVICE_KEY'),
            'region' => 'eu',
            'options' => [],
        ],

    ],

];
