<?php

namespace App\Containers\Dashboard\Tasks;

use Illuminate\Support\Facades\Log;

/**
 * Фильтрация писем по отправителю
 */
class FilterBySenderTask
{
    /**
     * Отфильтровать письма по разрешённым отправителям
     *
     * @param array $emails Массив писем
     * @param array|null $allowedSenders Whitelist email-адресов
     * @return array Отфильтрованные письма
     */
    public function run(array $emails, ?array $allowedSenders = null): array
    {
        // Если whitelist не передан, берём из конфига
        $allowedSenders = $allowedSenders ?? config('email-news.allowed_senders', []);
        
        // Фильтруем пустые значения
        $allowedSenders = array_filter($allowedSenders, fn($email) => !empty($email));

        // Если список пуст, используем editor_email
        if (empty($allowedSenders)) {
            $editorEmail = config('email-news.editor_email');
            if ($editorEmail) {
                $allowedSenders = [$editorEmail];
            }
        }

        Log::info('[FilterBySenderTask] Фильтрация писем', [
            'total_emails' => count($emails),
            'allowed_senders' => $allowedSenders,
        ]);

        if (empty($allowedSenders)) {
            Log::warning('[FilterBySenderTask] Не указан разрешённый отправитель, пропускаем все письма');
            return [];
        }

        $filtered = [];
        $skippedCount = 0;

        foreach ($emails as $email) {
            $fromEmail = $email['from_email'];

            // Проверяем, есть ли отправитель в whitelist
            if (!in_array($fromEmail, $allowedSenders, true)) {
                $skippedCount++;
                
                // Логируем только если включено логирование
                if (config('email-news.log_skipped_emails', true)) {
                    Log::debug('[FilterBySenderTask] Пропущено письмо от неразрешённого отправителя', [
                        'from' => $fromEmail,
                        'subject' => $email['subject'],
                        'date' => $email['date'],
                    ]);
                }
                continue;
            }

            $filtered[] = $email;
        }

        Log::info('[FilterBySenderTask] Фильтрация завершена', [
            'total' => count($emails),
            'filtered' => count($filtered),
            'skipped' => $skippedCount,
        ]);

        return $filtered;
    }

    /**
     * Проверить конкретный email на разрешение
     *
     * @param string $email Email для проверки
     * @return bool
     */
    public function isAllowed(string $email): bool
    {
        $allowedSenders = config('email-news.allowed_senders', []);
        $allowedSenders = array_filter($allowedSenders, fn($e) => !empty($e));

        if (empty($allowedSenders)) {
            $allowedSenders = [config('email-news.editor_email')];
        }

        return in_array($email, $allowedSenders, true);
    }
}
