<?php

namespace App\Containers\Dashboard\Tasks;

use App\Containers\Dashboard\Data\EmailAttachmentData;
use App\Containers\Dashboard\Exceptions\EmailFetchException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Webklex\PHPIMAP\Attachment;

/**
 * Загрузка и сохранение вложений из письма
 */
class DownloadAttachmentsTask
{
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

        Log::info('[DownloadAttachmentsTask] Начало загрузки вложений', [
            'message_id' => $message->getMessageId(),
            'disk' => $disk,
        ]);

        $attachments = $message->getAttachments();

        if (empty($attachments)) {
            Log::warning('[DownloadAttachmentsTask] Вложения не найдены');
            throw EmailFetchException::noAttachmentsFound();
        }

        Log::info('[DownloadAttachmentsTask] Найдено вложений', [
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
                    $savedAttachments[] = EmailAttachmentData::fromFile(
                        path: $savedPath,
                        originalFilename: $attachment->getName(),
                        mimeType: $attachment->getContentType(),
                        size: $attachment->getSize(),
                        contentId: $attachment->getContentId(),
                    );

                    Log::info('[DownloadAttachmentsTask] Вложение сохранено', [
                        'filename' => $attachment->getName(),
                        'path' => $savedPath,
                        'size' => $attachment->getSize(),
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

        Log::info('[DownloadAttachmentsTask] Загрузка вложений завершена', [
            'saved_count' => count($savedAttachments),
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
        // Декодируем MIME-кодировку (=?UTF-8?B?...?=)
        $decodedName = mb_decode_mimeheader($originalName) ?: $originalName;
        
        // Если не декодировалось, пробуем другой метод
        if ($decodedName === $originalName && str_contains($originalName, '=?')) {
            $decodedName = iconv_mime_decode($originalName, ICONV_MIME_DECODE_CONTINUE_ON_ERROR, 'UTF-8') ?: $originalName;
        }
        
        // Очищаем имя от специальных символов
        $decodedName = preg_replace('/[^a-zA-Z0-9_\-\p{L}.]/u', '_', $decodedName);
        
        // Удаляем множественные подчёркивания
        $decodedName = preg_replace('/_+/', '_', $decodedName);
        
        // Получаем расширение
        $extension = strtolower(pathinfo($decodedName, PATHINFO_EXTENSION));
        
        // Если расширение пустое, пробуем определить из оригинального имени
        if (empty($extension) && str_contains($originalName, '.docx')) {
            $extension = 'docx';
        } elseif (empty($extension) && str_contains($originalName, '.doc')) {
            $extension = 'doc';
        } elseif (empty($extension) && str_contains($originalName, '.pdf')) {
            $extension = 'pdf';
        }
        
        // Генерируем уникальное имя
        $basename = pathinfo($decodedName, PATHINFO_FILENAME);
        $basename = mb_substr($basename, 0, 100); // Ограничиваем длину
        
        return $basename . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . ($extension ?: 'bin');
    }
}
