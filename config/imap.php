<?php

/*
 * Set any customizations webklex/php-imap you would like to use.
 */
return [
    /*
    |--------------------------------------------------------------------------
    | Default date format
    |--------------------------------------------------------------------------
    |
    | The default date format is used to convert any given Carbon::class object
    | into a valid date string.
    |
    */
    'date_format' => 'd-M-y',

    /*
    |--------------------------------------------------------------------------
    | Default account
    |--------------------------------------------------------------------------
    |
    | The default account identifier. It will be used as default for any missing account parameters.
    | If however the default account is missing a parameter the package default will be used.
    |
    */
    'default' => 'email_news',

    /*
    |--------------------------------------------------------------------------
    | Available accounts
    |--------------------------------------------------------------------------
    |
    | Please list all IMAP accounts which you are planning to use within the
    | array below.
    |
    */
    'accounts' => [
        'email_news' => [
            'host'  => env('EMAIL_NEWS_IMAP_HOST', 'imap.mail.ru'),
            'port'  => env('EMAIL_NEWS_IMAP_PORT', 993),
            'protocol'  => env('EMAIL_NEWS_PROTOCOL', 'imap'),
            'encryption'  => env('EMAIL_NEWS_ENCRYPTION', 'ssl'),
            'validate_cert'  => env('EMAIL_NEWS_VALIDATE_CERT', true),
            'username'  => env('EMAIL_NEWS_IMAP_USER', ''),
            'password'  => env('EMAIL_NEWS_IMAP_PASS', ''),
            'authentication'  => null,
            'proxy' => [
                'socket' => null,
                'request_fulluri' => false,
                'username' => null,
                'password' => null,
            ],
            'timeout' => 30,
            'extensions' => [],
        ],

        /*
        |--------------------------------------------------------------------------
        | Default account
        |--------------------------------------------------------------------------
        |
        | This account will be used as default if no account is specified.
        |
        */
        'default' => [
            'host'  => 'localhost',
            'port'  => 993,
            'protocol'  => 'imap',
            'encryption'  => 'ssl',
            'validate_cert'  => true,
            'username'  => '',
            'password'  => '',
            'authentication' => null,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Available IMAP options
    |--------------------------------------------------------------------------
    |
    | Available php imap config parameters are listed below
    |
    */
    'options' => [
        // Append option to the connection string
        'append' => null,

        // IMAP open options
        'open' => [
            'DISABLE_AUTHENTICATOR' => 'GSSAPI',
        ],

        // OPTIMIZATION: Fetch method - FT_PEEK prevents marking messages as read automatically
        'fetch' => \Webklex\PHPIMAP\IMAP::FT_PEEK,

        // OPTIMIZATION: Use UID as message key for better reliability
        'message_key' => 'id',

        // NOTE: fetch_body и fetch_flags контролируются на уровне Query (setFetchBody/setFetchFlags)
        // Глобальные настройки здесь не применяются, чтобы не ломать вложения

        // OPTIMIZATION: Limit number of messages fetched per query
        // Prevents memory overflow when processing large mailboxes
        'fetch_limit' => env('IMAP_FETCH_LIMIT', 20),
    ],

    /*
    |--------------------------------------------------------------------------
    | Available flags
    |--------------------------------------------------------------------------
    |
    | List of available flags
    |
    */
    'flags' => [
        'recent' => '\Recent',
        'flagged' => '\Flagged',
        'answered' => '\Answered',
        'deleted' => '\Deleted',
        'seen' => '\Seen',
        'draft' => '\Draft',
    ],

    /*
    |--------------------------------------------------------------------------
    | Available events
    |--------------------------------------------------------------------------
    |
    */
    'events' => [
        'message' => [
            'new' => \Webklex\PHPIMAP\Events\MessageNewEvent::class,
            'moved' => \Webklex\PHPIMAP\Events\MessageMovedEvent::class,
        ],
        'folder' => [
            'new' => \Webklex\PHPIMAP\Events\FolderNewEvent::class,
            'moved' => \Webklex\PHPIMAP\Events\FolderMovedEvent::class,
            'deleted' => \Webklex\PHPIMAP\Events\FolderDeletedEvent::class,
        ],
        'flag' => [
            'new' => \Webklex\PHPIMAP\Events\FlagNewEvent::class,
            'deleted' => \Webklex\PHPIMAP\Events\FlagDeletedEvent::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Available decoding options
    |--------------------------------------------------------------------------
    |
    | Available php imap config parameters are listed below
    |
    */
    'decoding' => [
        'options' => [
            'UTF7-IMAP' => env('IMAP_DECODING_UTF7_IMAP', true),
            'attachments' => env('IMAP_DECODING_ATTACHMENTS', true),
        ],
        'ignore' => [
            'spelling' => env('IMAP_DECODING_IGNORE_SPELLING', true),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Available masking options
    |--------------------------------------------------------------------------
    |
    | By using the masking option you can define which class should be used
    |
    */
    'masking' => [
        'message' => \Webklex\PHPIMAP\Support\Masks\MessageMask::class,
        'attachment' => \Webklex\PHPIMAP\Support\Masks\AttachmentMask::class,
    ],
];
