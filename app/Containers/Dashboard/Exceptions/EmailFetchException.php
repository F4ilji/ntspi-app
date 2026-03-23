<?php

namespace App\Containers\Dashboard\Exceptions;

use Exception;

/**
 * Исключение для ошибок при получении Email
 */
class EmailFetchException extends Exception
{
    /**
     * Ошибка подключения к IMAP
     */
    public static function connectionFailed(string $message = ''): self
    {
        return new self(
            message: 'Не удалось подключиться к IMAP-серверу. ' . ($message ?: ''),
            code: 1001,
        );
    }

    /**
     * Папка не найдена
     */
    public static function folderNotFound(string $folder): self
    {
        return new self(
            message: "IMAP-папка не найдена: {$folder}",
            code: 1002,
        );
    }

    /**
     * Нет писем для обработки
     */
    public static function noEmailsFound(): self
    {
        return new self(
            message: 'Не найдено писем для обработки',
            code: 1003,
        );
    }

    /**
     * Ошибка при загрузке вложения
     */
    public static function attachmentDownloadFailed(string $filename, string $message = ''): self
    {
        return new self(
            message: "Не удалось загрузить вложение: {$filename}. " . ($message ?: ''),
            code: 1004,
        );
    }

    /**
     * Ошибка: письмо не от редактора
     */
    public static function senderNotAllowed(string $senderEmail): self
    {
        return new self(
            message: "Письмо получено от неразрешённого отправителя: {$senderEmail}",
            code: 1005,
        );
    }

    /**
     * Ошибка: нет вложений
     */
    public static function noAttachmentsFound(): self
    {
        return new self(
            message: 'В письме не найдено вложений',
            code: 1006,
        );
    }

    /**
     * Ошибка: функционал отключён
     */
    public static function featureDisabled(): self
    {
        return new self(
            message: 'Функция получения новостей из Email отключена в конфиге',
            code: 1007,
        );
    }
}
