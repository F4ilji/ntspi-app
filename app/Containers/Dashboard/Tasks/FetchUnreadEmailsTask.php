<?php

namespace App\Containers\Dashboard\Tasks;

use Webklex\PHPIMAP\Folder;
use Illuminate\Support\Facades\Log;

/**
 * Получение непрочитанных писем из папки
 */
class FetchUnreadEmailsTask
{
    /**
     * Получить непрочитанные письма
     *
     * @param Folder $folder IMAP папка
     * @return array Массив писем
     */
    public function run(Folder $folder): array
    {
        Log::info('[FetchUnreadEmailsTask] Получение непрочитанных писем', [
            'folder' => $folder->full_name ?? $folder->name ?? 'unknown',
        ]);

        try {
            // Получаем все непрочитанные сообщения
            $messages = $folder->messages()->unseen()->get();

            $emails = [];
            foreach ($messages as $message) {
                $from = $message->getFrom()[0] ?? null;
                $subject = $message->getSubject();
                $date = $message->getDate();

                $emails[] = [
                    'message' => $message,
                    'message_id' => $message->getMessageId(),
                    'from_email' => $from?->mail ?? null,
                    'from_name' => $from?->name ?? null,
                    'subject' => $subject,
                    'date' => $date,
                    'has_attachments' => $message->hasAttachments(),
                ];
            }

            Log::info('[FetchUnreadEmailsTask] Получены письма', [
                'count' => count($emails),
                'folder' => $folder->full_name ?? $folder->name ?? 'unknown',
            ]);

            return $emails;
        } catch (\Exception $e) {
            Log::error('[FetchUnreadEmailsTask] Ошибка получения писем', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }
}
