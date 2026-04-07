<?php

namespace App\Containers\Dashboard\Tasks\Email;

use App\Containers\Dashboard\Data\EmailAttachmentData;
use App\Containers\Dashboard\Exceptions\EmailFetchException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Webklex\PHPIMAP\Attachment;

/**
 * Загрузка и сохранение вложений из письма
 *
 * OPTIMIZATION: Использует lazy loading для тел сообщений.
 * Вложения загружаются напрямую без загрузки полного тела письма.
 */
class DownloadAttachmentsTask
{
    /**
     * Порог памяти для логирования (MB)
     */
    private const MEMORY_LOG_THRESHOLD = 100 * 1024 * 1024; // 100MB

    /**
     * Скачать и сохранить вложения
     *
     * @param object $message IMAP сообщение
     * @param string|null $disk Диск для сохранения
     * @return array<EmailAttachmentData>
     * @throws EmailFetchException
     */
    public function run(object $message, ?string $disk = null): array
    {
        $disk = $disk ?? config('email-news.attachments_folder', 'email_attachments');

        Log::debug('[DownloadAttachmentsTask] Начало загрузки вложений', [
            'message_id' => $message->getMessageId(),
            'disk' => $disk,
            'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2) . 'MB',
        ]);

        // OPTIMIZATION: Загружаем вложения напрямую (без загрузки тела письма)
        // Метод getAttachments() использует структуру письма, а не raw_body
        $attachments = $message->getAttachments();

        if (empty($attachments)) {
            Log::warning('[DownloadAttachmentsTask] Вложения не найдены');
            throw EmailFetchException::noAttachmentsFound();
        }

        Log::debug('[DownloadAttachmentsTask] Найдено вложений', [
            'count' => count($attachments),
        ]);

        $savedAttachments = [];
        $maxSize = config('email-news.max_attachment_size', 41943040); // 40MB по умолчанию

        /** @var Attachment $attachment */
        foreach ($attachments as $attachment) {
            try {
                // Проверяем размер
                if ($maxSize > 0 && $attachment->getSize() > $maxSize) {
                    Log::warning('[DownloadAttachmentsTask] Вложение превышает максимальный размер', [
                        'filename' => $attachment->getName(),
                        'size' => $attachment->getSize(),
                        'max_size' => $maxSize,
                    ]);
                    continue;
                }

                // Сохраняем вложение
                $savedPath = $this->saveAttachment($attachment, $disk);

                if ($savedPath) {
                    // Декодируем MIME-имя для корректного определения расширения
                    $decodedFilename = $this->decodeMimeFilename($attachment->getName());

                    $savedAttachments[] = EmailAttachmentData::fromFile(
                        path: $savedPath,
                        originalFilename: $decodedFilename,
                        mimeType: $attachment->getContentType(),
                        size: $attachment->getSize(),
                        contentId: $attachment->getContentId(),
                    );

                    Log::debug('[DownloadAttachmentsTask] Вложение сохранено', [
                        'filename' => $attachment->getName(),
                        'path' => $savedPath,
                        'size' => $attachment->getSize(),
                    ]);
                }

                // OPTIMIZATION: Логирование памяти для больших вложений
                if (memory_get_usage(true) > self::MEMORY_LOG_THRESHOLD) {
                    Log::debug('[DownloadAttachmentsTask] Высокое использование памяти', [
                        'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2) . 'MB',
                        'peak_memory' => round(memory_get_peak_usage(true) / 1024 / 1024, 2) . 'MB',
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('[DownloadAttachmentsTask] Ошибка сохранения вложения', [
                    'filename' => $attachment->getName(),
                    'error' => $e->getMessage(),
                ]);
                // Продолжаем обработку остальных вложений
            }
        }

        if (empty($savedAttachments)) {
            Log::error('[DownloadAttachmentsTask] Не удалось сохранить ни одно вложение');
            throw EmailFetchException::noAttachmentsFound();
        }

        Log::debug('[DownloadAttachmentsTask] Загрузка вложений завершена', [
            'saved_count' => count($savedAttachments),
            'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2) . 'MB',
        ]);

        return $savedAttachments;
    }

    /**
     * Сохранить вложение на диск
     *
     * @param Attachment $attachment Вложение
     * @param string $disk Диск для сохранения
     * @return string|null Путь к сохранённому файлу
     */
    private function saveAttachment(Attachment $attachment, string $disk): ?string
    {
        $filename = $this->generateUniqueFilename($attachment->getName());
        $savePath = storage_path('app/' . $disk);

        // Создаём директорию, если не существует
        if (!is_dir($savePath)) {
            mkdir($savePath, 0755, true);
        }

        // Сохраняем вложение (метод save принимает только путь и имя файла)
        try {
            $savedPath = $attachment->save($savePath, $filename);

            if ($savedPath) {
                // Возвращаем относительный путь для сохранения в БД
                return $disk . '/' . $filename;
            }
        } catch (\Exception $e) {
            Log::error('[DownloadAttachmentsTask:saveAttachment] Ошибка сохранения', [
                'filename' => $attachment->getName(),
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Сгенерировать уникальное имя файла
     *
     * @param string $originalName Оригинальное имя файла
     * @return string
     */
    private function generateUniqueFilename(string $originalName): string
    {
        $decodedName = $this->decodeMimeFilename($originalName);

        // Очищаем имя от специальных символов
        $decodedName = preg_replace('/[^a-zA-Z0-9_\-\p{L}.]/u', '_', $decodedName);

        // Удаляем множественные подчёркивания
        $decodedName = preg_replace('/_+/', '_', $decodedName);

        // Получаем расширение
        $extension = strtolower(pathinfo($decodedName, PATHINFO_EXTENSION));

        // SECURITY: Whitelist разрешенных расширений для предотвращения path traversal
        $allowedExtensions = [
            'doc', 'docx', 'pdf', 'txt', 'rtf', 'odt',  // Документы
            'png', 'jpg', 'jpeg', 'gif', 'webp', 'bmp', // Изображения
            'xls', 'xlsx', 'ppt', 'pptx',               // Офисные файлы
            'zip', 'rar', '7z',                          // Архивы
        ];

        if (empty($extension) || !in_array($extension, $allowedExtensions, true)) {
            // Пробуем определить из оригинального имени
            if (str_contains(strtolower($originalName), '.docx')) {
                $extension = 'docx';
            } elseif (str_contains(strtolower($originalName), '.doc')) {
                $extension = 'doc';
            } elseif (str_contains(strtolower($originalName), '.pdf')) {
                $extension = 'pdf';
            } else {
                $extension = 'bin'; // Безопасное расширение по умолчанию
            }
        }

        // Генерируем уникальное имя
        $basename = pathinfo($decodedName, PATHINFO_FILENAME);
        $basename = mb_substr($basename, 0, 100); // Ограничиваем длину

        return $basename . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
    }

    /**
     * Декодировать MIME-кодированное имя файла
     *
     * @param string $filename Имя файла в MIME-кодировке
     * @return string Декодированное имя файла
     */
    private function decodeMimeFilename(string $filename): string
    {
        // Декодируем MIME-кодировку (=?UTF-8?B?...?=)
        $decodedName = mb_decode_mimeheader($filename) ?: $filename;

        // Если не декодировалось, пробуем другой метод
        if ($decodedName === $filename && str_contains($filename, '=?')) {
            $decodedName = iconv_mime_decode($filename, ICONV_MIME_DECODE_CONTINUE_ON_ERROR, 'UTF-8') ?: $filename;
        }

        return $decodedName;
    }
}
