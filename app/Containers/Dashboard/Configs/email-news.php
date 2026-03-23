<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Email News Fetching Settings
    |--------------------------------------------------------------------------
    |
    | Настройки для автоматического получения новостей из Email
    |
    */

    // Включить ли функционал
    'enabled' => env('EMAIL_NEWS_ENABLED', true),

    // Имя аккаунта из config/imap.php
    'imap_account' => 'email_news',

    // Email редактора (единственный разрешённый отправитель)
    'editor_email' => env('EMAIL_NEWS_SENDER_EMAIL'),

    // Whitelist email-адресов (если редакторов несколько)
    'allowed_senders' => [
        env('EMAIL_NEWS_SENDER_EMAIL'),
        // Можно добавить дополнительные email
        // env('EMAIL_NEWS_SENDER_EMAIL_2'),
    ],

    // IMAP папка для проверки
    'folder' => env('EMAIL_NEWS_FOLDER', 'INBOX'),

    // Что делать с письмами после обработки
    'mark_as_read' => env('EMAIL_NEWS_MARK_AS_READ', true),

    // Перемещать ли обработанные письма в другую папку (или null)
    'move_to_folder' => env('EMAIL_NEWS_MOVE_TO', null),

    // Папка для сохранения вложений
    'attachments_folder' => 'email_attachments',

    // Максимальный размер вложения (в байтах), 0 = без ограничений
    'max_attachment_size' => env('EMAIL_NEWS_MAX_ATTACHMENT_SIZE', 41943040), // 40MB

    // Логировать ли отклонённые письма (не от редактора)
    'log_skipped_emails' => env('EMAIL_NEWS_LOG_SKIPPED', true),
];
