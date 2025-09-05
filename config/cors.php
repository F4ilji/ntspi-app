<?php

return [

    'paths' => ['*'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE'],

    'allowed_origins' => explode(',', env('CORS_ALLOWED_ORIGINS', 'https://www.ntspi.ru')),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Content-Type', 'X-Requested-With', 'Authorization'],

    'exposed_headers' => [],

    'max_age' => 3600,

    'supports_credentials' => true,
];