<?php

namespace App\Containers\Dashboard\Tasks;

use Illuminate\Support\Facades\Log;

/**
 * Пометка письма как прочитанного
 */
class MarkEmailAsReadTask
{
    /**
     * Пометить письмо как прочитанное
     *
     * @param object $message IMAP сообщение
     * @return bool
     */
    public function run(object $message): bool
    {
        Log::info('[MarkEmailAsReadTask] Пометка письма как прочитанного', [
            'message_id' => $message->getMessageId(),
            'subject' => $message->getSubject(),
        ]);

        try {
            $message->setFlag('Seen');

            Log::info('[MarkEmailAsReadTask] Письмо помечено как прочитанное');

            return true;
        } catch (\Exception $e) {
            Log::error('[MarkEmailAsReadTask] Ошибка пометки письма', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Пометить письмо как прочитанное и переместить в другую папку
     *
     * @param object $message IMAP сообщение
     * @param string $targetFolder Целевая папка
     * @return bool
     */
    public function markAndMove(object $message, string $targetFolder): bool
    {
        Log::info('[MarkEmailAsReadTask] Пометка и перемещение письма', [
            'message_id' => $message->getMessageId(),
            'target_folder' => $targetFolder,
        ]);

        try {
            // Помечаем как прочитанное
            $message->setFlag('Seen');

            // Перемещаем в другую папку
            $message->moveToFolder($targetFolder);

            Log::info('[MarkEmailAsReadTask] Письмо обработано и перемещено', [
                'target_folder' => $targetFolder,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('[MarkEmailAsReadTask] Ошибка обработки письма', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
