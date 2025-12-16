<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'yandex_metrika' => [
        'id' => env('YANDEX_METRIKA_ID'),
    ],

    'vk' => [
        'app_id' => env('VK_APP_ID'),
        'service_key' => env('SERVICE_ACCESS_VK_KEY'),
        'wall_token' => env('WALL_ACCESS_VK_TOKEN'),
        'public_id' => env('PUBLIC_ID'),
        'public_domain' => env('PUBLIC_DOMAIN'),
        'redirect_uri' => env('VK_REDIRECT_URI'),
        'redirect_uri_after_auth' => env('VK_REDIRECT_URI_AFTER_AUTH'),
    ],

    'vicon' => [
        'token' => env('VICON_TOKEN'),
        'api_url' => 'https://db-nica.ru/api/v1',
    ],


];
