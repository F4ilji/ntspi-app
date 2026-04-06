<?php

namespace App\Containers\Dashboard\Tasks\Email;

use App\Containers\Dashboard\Exceptions\EmailFetchException;
use App\Containers\Dashboard\Traits\MemoryAwareTrait;
use Webklex\PHPIMAP\Folder;
use Illuminate\Support\Facades\Log;

/**
 * Получение непрочитанных писем из папки
 *
 * OPTIMIZATION: Загружает только заголовки писем (без тел и вложений).
 * Тела загружаются лениво только при необходимости в DownloadAttachmentsTask.
 */
class FetchUnreadEmailsTask
{
    use MemoryAwareTrait;

    /**
     * Получить непрочитанные письма
     *
     * @param Folder $folder IMAP папка
     * @param int|null $limit Лимит сообщений (null = использовать конфиг)
     * @return array Массив писем (только метаданные, без тел)
     * @throws EmailFetchException При ошибках IMAP-подключения
     */
    public function run(Folder $folder, ?int $limit = null): array
    {
        $limit = $limit ?? config('imap.options.fetch_limit', 20);

        Log::info('[FetchUnreadEmailsTask] Получение непрочитанных писем', [
            'folder' => $folder->full_name ?? $folder->name ?? 'unknown',
            'limit' => $limit,
        ]);

        try {
            // NOTE: setFetchBody(true) необходим для загрузки структуры письма (вложения)
            // Контроль памяти осуществляется через limit() и batch-обработку в Action
            $query = $folder->messages()
                ->unseen()
                ->setFetchBody(true)  // Загружаем структуру (нужна для вложений)
                ->setFetchFlags(false); // Флаги не нужны

            // Применяем лимит для предотвращения переполнения памяти
            if ($limit > 0) {
                $query->limit($limit);
            }

            $messages = $query->get();

            $emails = [];
            foreach ($messages as $message) {
                // OPTIMIZATION: Извлекаем только метаданные (не загружаем тело)
                $from = $message->getFrom()[0] ?? null;
                $subjectAttr = $message->getSubject();

                $emails[] = [
                    'message' => $message,
                    'message_id' => $message->getMessageId(),
                    'uid' => $message->getUid(),
                    'from_email' => $from?->mail ?? null,
                    'from_name' => $from?->name ?? null,
                    // FIX: Attribute объект нужно конвертировать в строку
                    'subject' => $subjectAttr instanceof \Webklex\PHPIMAP\Attribute ? (string) $subjectAttr : ($subjectAttr ?? 'Без темы'),
                    'date' => $message->getDate(),
                    // NOTE: hasAttachments() может быть неточным без загрузки структуры
                    'has_attachments' => $message->hasAttachments(),
                    'size' => $message->getSize(),
                ];
            }

            // OPTIMIZATION: Логируем память после загрузки
            $this->logMemoryUsage('after_fetching_emails', [
                'emails_count' => count($emails),
            ]);

            Log::info('[FetchUnreadEmailsTask] Получены письма', [
                'count' => count($emails),
                'folder' => $folder->full_name ?? $folder->name ?? 'unknown',
            ]);

            return $emails;
        } catch (EmailFetchException $e) {
            // Пробрасываем наши кастомные исключения
            throw $e;
        } catch (\Exception $e) {
            Log::error('[FetchUnreadEmailsTask] Ошибка получения писем', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Пробрасываем как EmailFetchException для правильной обработки в Action
            throw EmailFetchException::connectionFailed('Failed to fetch emails: ' . $e->getMessage(), 0, $e);
        }
    }
}
