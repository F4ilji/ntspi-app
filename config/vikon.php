<?php

return [

    'client_id' => env('VIKON_CLIENT_ID', '542'),
    'client_secret' => env('VIKON_CLIENT_SECRET', ''),
    'vuz_id' => env('VIKON_VUZ_ID', '16775'),

    'api_domain' => env('VIKON_API_DOMAIN', 'https://db-nica.ru/'),
    'auth_domain' => env('VIKON_AUTH_DOMAIN', 'https://auth.db-nica.ru/'),
    'filemanager_domain' => env('VIKON_FILEMANAGER_DOMAIN', 'https://file.db-nica.ru/'),

    'current_version' => env('VIKON_CURRENT_VERSION', '1.0.0'),

    'http_timeout' => env('VIKON_HTTP_TIMEOUT', 60),
    'http_retries' => env('VIKON_HTTP_RETRIES', 3),

    'storage_path' => storage_path('app/vikon'),

    'domain_resolve' => env('VIKON_DOMAIN_RESOLVE', false),
    'vikon_domain_resolve_ip' => env('VIKON_DOMAIN_RESOLVE_IP', '62.76.112.192'),
    'fm_domain_resolve_ip' => env('VIKON_FM_DOMAIN_RESOLVE_IP', '62.76.112.192'),

    'modules' => [
        1 => [
            'name' => 'Сведения',
            'path' => 'sveden',
            'allowed_folders' => [
                'assets', 'files_zaglushka', 'common', 'struct', 'document',
                'education', 'managers', 'employees', 'objects', 'paid_edu',
                'budget', 'vacant', 'grants', 'inter', 'catering',
                'eduStandarts', 'corruption', 'antiterrorism', 'files',
                'update', 'index.html', '.vikon', '.htaccess',
            ],
        ],
        2 => [
            'name' => 'Абитуриент',
            'path' => 'abitur',
            'init_only' => true,
            'allowed_folders' => ['abitur'],
        ],
        6 => [
            'name' => 'ВСОКО',
            'path' => 'vsoko',
            'allowed_folders' => [
                'assets', 'general', 'structure', 'faq', 'procedures',
                'results-and-reports', 'plans', 'survey', 'files',
                '.vikon', 'index.html', '.htaccess',
            ],
        ],
    ],

    'poll_interval' => (int) env('VIKON_POLL_INTERVAL', 3),
    'poll_max_attempts' => (int) env('VIKON_POLL_MAX_ATTEMPTS', 50),

];
