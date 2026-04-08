<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Vikon API Configuration
    |--------------------------------------------------------------------------
    |
    | Credentials and endpoints for Vikon integration (db-nica.ru).
    | All sensitive data moved from public/vikon_core to .env
    |
    */

    'client_id' => env('VIKON_CLIENT_ID', '542'),

    'client_secret' => env('VIKON_CLIENT_SECRET', ''),

    'vuz_id' => env('VIKON_VUZ_ID', '16775'),

    /*
    |--------------------------------------------------------------------------
    | Current Version
    |--------------------------------------------------------------------------
    */

    'current_version' => env('VIKON_CURRENT_VERSION', file_get_contents(base_path('vikon_version.txt')) ?: '1.0.0'),

    /*
    |--------------------------------------------------------------------------
    | API Endpoints
    |--------------------------------------------------------------------------
    */

    'api_domain' => env('VIKON_API_DOMAIN', 'https://db-nica.ru/'),

    'auth_domain' => env('VIKON_AUTH_DOMAIN', 'https://auth.db-nica.ru/'),

    'filemanager_domain' => env('VIKON_FILEMANAGER_DOMAIN', 'https://file.db-nica.ru/'),

    /*
    |--------------------------------------------------------------------------
    | Module Configuration
    |--------------------------------------------------------------------------
    |
    | Module IDs and their deployment paths
    | 1 = Sveden (Сведения)
    | 2 = Abitur (Абитуриент)
    | 6 = VSOKO (ВСОКО)
    |
    */

    'modules' => [
        1 => [
            'name' => 'Сведения об образовательной организации',
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

    /*
    |--------------------------------------------------------------------------
    | HTTP Client Settings
    |--------------------------------------------------------------------------
    */

    'http_timeout' => env('VIKON_HTTP_TIMEOUT', 60),

    'http_retries' => env('VIKON_HTTP_RETRIES', 3),

    /*
    |--------------------------------------------------------------------------
    | Update Settings
    |--------------------------------------------------------------------------
    */

    'storage_path' => storage_path('app/vikon'),

    'max_upload_size' => env('VIKON_MAX_UPLOAD_SIZE', 50 * 1024 * 1024), // 50MB

];
