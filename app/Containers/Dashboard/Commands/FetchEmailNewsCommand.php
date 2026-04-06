<?php

namespace App\Containers\Dashboard\Commands;

use App\Containers\Dashboard\Actions\EmailNews\FetchEmailNewsAction;
use App\Containers\Dashboard\Exceptions\EmailFetchException;
use App\Containers\Dashboard\Jobs\ProcessEmailNewsJob;
use App\Ship\Abstracts\Commands\ConsoleCommand;
use Illuminate\Support\Facades\Log;

/**
 * Artisan команда для получения новостей из Email
 *
 * OPTIMIZATION: Поддерживает два режима:
 * 1. Синхронный (по умолчанию) — для backward compatibility
 * 2. Асинхронный (--async) — dispatch в queue worker (рекомендуется)
 */
class FetchEmailNewsCommand extends ConsoleCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:fetch-news
                            {--force : Принудительный запуск, даже если отключено в конфиге}
                            {--log : Выводить подробный лог в консоль}
                            {--async : Отправить задачу в очередь (рекомендуется для production)}
                            {--queue= : Имя очереди (по умолчанию "default")}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Получение новостей из Email (IMAP) и создание черновиков';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(FetchEmailNewsAction $fetchEmailNewsAction): int
    {
        $this->info('📧 Запуск получения новостей из Email...');

        $force = $this->option('force');
        $verbose = $this->option('log');
        $async = $this->option('async');
        $queue = $this->option('queue') ?? 'default';

        // Проверяем, включена ли функция
        if (!config('email-news.enabled', true) && !$force) {
            $this->error('❌ Функция отключена в конфиге (EMAIL_NEWS_ENABLED=false)');
            $this->warn('Используйте --force для принудительного запуска');
            return 1;
        }

        try {
            // OPTIMIZATION: Асинхронный режим через queue worker
            if ($async) {
                return $this->handleAsync($queue, $verbose);
            }

            // Синхронный режим (backward compatibility)
            return $this->handleSync($fetchEmailNewsAction, $verbose);
        } catch (EmailFetchException $e) {
            $this->error('❌ Ошибка Email: ' . $e->getMessage());
            Log::error('[FetchEmailNewsCommand] EmailFetchException', [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);

            return 1;
        } catch (\Exception $e) {
            $this->error('❌ Критическая ошибка: ' . $e->getMessage());
            $this->warn('Проверьте логи: storage/logs/laravel.log');

            Log::error('[FetchEmailNewsCommand] Critical Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return 1;
        }
    }

    /**
     * Синхронная обработка (backward compatibility)
     *
     * @param FetchEmailNewsAction $fetchEmailNewsAction
     * @param bool $verbose
     * @return int
     */
    private function handleSync(FetchEmailNewsAction $fetchEmailNewsAction, bool $verbose): int
    {
        $result = $fetchEmailNewsAction->run();

        // Вывод результатов
        $this->displayResults($result, $verbose);

        return 0;
    }

    /**
     * Асинхронная обработка через queue worker
     *
     * @param string $queue
     * @param bool $verbose
     * @return int
     */
    private function handleAsync(string $queue, bool $verbose): int
    {
        $this->info("📤 Отправка задачи в очередь '{$queue}'...");

        $job = ProcessEmailNewsJob::dispatch()->onQueue($queue);

        $this->info('✅ Задача отправлена в очередь');
        $this->line("  Job ID: {$job->getJobId()}");
        $this->newLine();
        $this->info('📝 Мониторинг выполнения:');
        $this->line("  php artisan queue:monitor {$queue}");
        $this->line("  tail -f storage/logs/laravel.log | grep ProcessEmailNewsJob");

        if ($verbose) {
            $this->newLine();
            $this->info('ℹ️  Queue worker должен быть запущен:');
            $this->line('  docker exec ntspi-php php artisan queue:work --queue=' . $queue);
        }

        return 0;
    }

    /**
     * Вывод результатов обработки
     *
     * @param array $result
     * @param bool $verbose
     * @return void
     */
    private function displayResults(array $result, bool $verbose): void
    {
        $this->newLine();
        $this->line('📊 Результаты обработки:');
        $this->table(
            ['Метрика', 'Значение'],
            [
                ['Обработано писем', $result['processed_emails']],
                ['Пропущено (не редактор)', $result['skipped_emails']],
                ['Создано новостей', $result['created_posts']],
                ['Ошибок', count($result['errors'])],
                ['Batch-ей обработано', $result['batches_processed'] ?? 0],
                ['Пик памяти', $result['peak_memory'] ?? 'N/A'],
            ]
        );

        // Вывод созданных постов
        if (!empty($result['posts'])) {
            $this->newLine();
            $this->info('✅ Созданные новости:');
            foreach ($result['posts'] as $post) {
                $status = $post->status instanceof \BackedEnum ? $post->status->value : $post->status;
                $this->line("  • {$post->title} (ID: {$post->id}, статус: {$status})");
            }
        }

        // Вывод ошибок
        if (!empty($result['errors'])) {
            $this->newLine();
            $this->error('❌ Ошибки:');
            foreach ($result['errors'] as $error) {
                $this->warn("  • {$error['email_subject']}: {$error['error']}");
            }
        }

        // Подробный лог
        if ($verbose) {
            $this->newLine();
            $this->info('📝 Детальный лог доступен в storage/logs/laravel.log');
        }

        $this->newLine();
        $this->info('✅ Завершено!');
    }
}
