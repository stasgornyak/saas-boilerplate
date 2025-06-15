<?php

return [
    'default_client' => env('PAYMENT_CLIENT', 'mono'),

    'clients' => [
        'fondy' => [
            'accounts' => [
                'test' => [
                    'merchant_id' => 1396424,
                    'secret_key' => 'test',
                    'credit_key' => 'testcredit',
                ],
                'production' => [
                    'merchant_id' => 0,
                    'secret_key' => '',
                    'credit_key' => '',
                ],
            ],
        ],
        'mono' => [
            'base_url' => 'https://api.monobank.ua',
            'accounts' => [
                'test' => [
                    'token' => env('PAYMENT_MONO_TOKEN'),
                ],
                'production' => [
                    'token' => env('PAYMENT_MONO_TOKEN'),
                ],
            ],
        ],
    ],

    'callbacks' => [
        'frontend_path' => '/payments',
        'backend_route' => 'payments.callback',
    ],
];
