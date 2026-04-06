<?php

namespace App\Containers\Dashboard\Jobs;

use App\Containers\Dashboard\Actions\EmailNews\FetchEmailNewsAction;
use App\Containers\Dashboard\Exceptions\EmailFetchException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Queue Job для обработки email новостей
 *
 * OPTIMIZATION: Выносит тяжелую IMAP-обработку в queue worker,
 * предотвращая переполнение памяти в веб-процессах.
 *
 * Usage:
 *   ProcessEmailNewsJob::dispatch();
 *   ProcessEmailNewsJob::dispatch()->onQueue('email-processing');
 */
class ProcessEmailNewsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Максимальное время выполнения (секунды)
     */
    public $timeout = 300; // 5 минут

    /**
     * Максимальное количество попыток
     */
    public $tries = 3;

    /**
     * Количество секунд ожидания перед повторной попыткой
     */
    public $backoff = 60; // 1 минута

    /**
     * Создать новую задачу
     */
    public function __construct()
    {
        // Параметры не требуются, все берется из конфига
    }

    /**
     * Выполнить задачу
     *
     * @param FetchEmailNewsAction $fetchEmailNewsAction
     * @return void
     * @throws \Exception Если обработка не удалась (Laravel автоматически повторит)
     */
    public function handle(FetchEmailNewsAction $fetchEmailNewsAction): void
    {
        Log::info('[ProcessEmailNewsJob] Начало обработки job', [
            'job_id' => $this->job?->getJobId() ?? 'unknown',
            'attempt' => $this->attempts(),
            'memory_limit' => ini_get('memory_limit'),
        ]);

        // Выполняем обработку — если выбросит исключение, Laravel автоматически повторит job
        $result = $fetchEmailNewsAction->run();

        Log::info('[ProcessEmailNewsJob] Обработка завершена успешно', [
            'job_id' => $this->job?->getJobId() ?? 'unknown',
            'processed_emails' => $result['processed_emails'],
            'created_posts' => $result['created_posts'],
            'skipped_emails' => $result['skipped_emails'],
            'errors_count' => count($result['errors']),
            'batches_processed' => $result['batches_processed'] ?? 0,
            'peak_memory' => round(memory_get_peak_usage(true) / 1024 / 1024, 2) . 'MB',
        ]);

        // Если были ошибки, логируем их как warning
        if (!empty($result['errors'])) {
            foreach ($result['errors'] as $error) {
                Log::warning('[ProcessEmailNewsJob] Ошибка обработки письма', [
                    'subject' => $error['email_subject'] ?? 'unknown',
                    'error' => $error['error'],
                ]);
            }
        }
    }

    /**
     * Обработка проваленной задачи (вызывается Laravel после исчерпания попыток)
     *
     * @param \Throwable $exception
     * @return void
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical('[ProcessEmailNewsJob] Job окончательно провален', [
            'total_attempts' => $this->tries,
            'error' => $exception->getMessage(),
        ]);

        // Здесь можно отправить уведомление администратору
        // Notification::route('mail', 'admin@ntspi.ru')->notify(new EmailProcessingFailed($exception));
    }
}
