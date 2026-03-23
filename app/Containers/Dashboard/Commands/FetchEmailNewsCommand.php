<?php

namespace App\Containers\Dashboard\Commands;

use App\Containers\Dashboard\Actions\FetchEmailNewsAction;
use App\Containers\Dashboard\Exceptions\EmailFetchException;
use App\Ship\Abstracts\Commands\ConsoleCommand;
use Illuminate\Support\Facades\Log;

/**
 * Artisan команда для получения новостей из Email
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
                            {--log : Выводить подробный лог в консоль}';

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

        // Проверяем, включена ли функция
        if (!config('email-news.enabled', true) && !$force) {
            $this->error('❌ Функция отключена в конфиге (EMAIL_NEWS_ENABLED=false)');
            $this->warn('Используйте --force для принудительного запуска');
            return 1;
        }

        try {
            $result = $fetchEmailNewsAction->run();

            // Вывод результатов
            $this->newLine();
            $this->line('📊 Результаты обработки:');
            $this->table(
                ['Метрика', 'Значение'],
                [
                    ['Обработано писем', $result['processed_emails']],
                    ['Пропущено (не редактор)', $result['skipped_emails']],
                    ['Создано новостей', $result['created_posts']],
                    ['Ошибок', count($result['errors'])],
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

            return 0;
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
}
