<?php

namespace App\Containers\Dashboard\Actions\EmailNews;

use App\Containers\Dashboard\Data\EmailAttachmentData;
use App\Containers\Dashboard\Exceptions\EmailFetchException;
use App\Containers\Dashboard\Tasks\Email\ConnectToImapTask;
use App\Containers\Dashboard\Tasks\Email\DownloadAttachmentsTask;
use App\Containers\Dashboard\Tasks\Email\FetchUnreadEmailsTask;
use App\Containers\Dashboard\Tasks\Email\FilterBySenderTask;
use App\Containers\Dashboard\Tasks\Email\MarkEmailAsReadTask;
use App\Containers\Dashboard\Actions\EmailNews\ProcessMixedFilesAction;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Webklex\PHPIMAP\Client;
use Webklex\PHPIMAP\Folder;

/**
 * Оркестрация процесса получения новостей из Email
 *
 * OPTIMIZATION: Реализует batch-обработку писем для предотвращения переполнения памяти.
 * Каждое письмо обрабатывается отдельно с освобождением памяти после обработки.
 */
class FetchEmailNewsAction
{
    /**
     * Максимальное использование памяти (в байтах) перед принудительной сборкой мусора
     */
    private const MEMORY_THRESHOLD = 400 * 1024 * 1024; // 400MB

    /**
     * Размер batch-обработки (писем за один цикл)
     */
    private const BATCH_SIZE = 10;

    public function __construct(
        private readonly ConnectToImapTask $connectToImapTask,
        private readonly FetchUnreadEmailsTask $fetchUnreadEmailsTask,
        private readonly FilterBySenderTask $filterBySenderTask,
        private readonly DownloadAttachmentsTask $downloadAttachmentsTask,
        private readonly MarkEmailAsReadTask $markEmailAsReadTask,
        private readonly ProcessMixedFilesAction $processMixedFilesAction,
    ) {}

    /**
     * Выполнить получение и обработку новостей из Email
     *
     * @return array Результат обработки
     * @throws EmailFetchException
     */
    public function run(): array
    {
        // Проверяем, включена ли функция
        if (!config('email-news.enabled', true)) {
            Log::warning('[FetchEmailNewsAction] Функция отключена в конфиге');
            throw EmailFetchException::featureDisabled();
        }

        Log::info('[FetchEmailNewsAction] Начало получения новостей из Email');

        $result = [
            'processed_emails' => 0,
            'skipped_emails' => 0,
            'created_posts' => 0,
            'errors' => [],
            'posts' => [],
            'batches_processed' => 0,
        ];

        try {
            // Подключаемся к IMAP
            $client = $this->connectToImapTask->run();

            // Получаем папку
            $folder = $this->connectToImapTask->getFolder(
                $client,
                config('email-news.folder', 'INBOX')
            );

            // OPTIMIZATION: Batch-обработка с контролем памяти
            $hasMoreEmails = true;

            while ($hasMoreEmails) {
                Log::info('[FetchEmailNewsAction] Обработка batch', [
                    'batch' => $result['batches_processed'] + 1,
                    'batch_size' => self::BATCH_SIZE,
                    'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2) . 'MB',
                ]);

                // Получаем batch писем (без offset, т.к. письма помечаются как прочитанные)
                $emails = $this->fetchUnreadEmailsTask->run(
                    $folder,
                    self::BATCH_SIZE
                );

                if (empty($emails)) {
                    $hasMoreEmails = false;
                    Log::info('[FetchEmailNewsAction] Нет больше писем для обработки');
                    break;
                }

                // Фильтруем по отправителю
                $filteredEmails = $this->filterBySenderTask->run($emails);
                $result['skipped_emails'] += count($emails) - count($filteredEmails);

                // Обрабатываем каждое письмо в batch
                foreach ($filteredEmails as $email) {
                    $emailResult = $this->processEmail($email, $folder);

                    if ($emailResult['success']) {
                        $result['created_posts']++;
                        $result['posts'][] = $emailResult['post'];
                    } else {
                        $result['errors'][] = [
                            'email_subject' => $email['subject'] ?? 'unknown',
                            'error' => $emailResult['error'],
                        ];
                    }

                    $result['processed_emails']++;
                }

                $result['batches_processed']++;

                // OPTIMIZATION: Принудительная сборка мусора после каждого batch
                $this->collectGarbageIfNeeded();
            }

            Log::info('[FetchEmailNewsAction] Завершено', [
                'processed' => $result['processed_emails'],
                'created_posts' => $result['created_posts'],
                'skipped' => $result['skipped_emails'],
                'errors_count' => count($result['errors']),
                'batches' => $result['batches_processed'],
                'peak_memory' => round(memory_get_peak_usage(true) / 1024 / 1024, 2) . 'MB',
            ]);

            // Добавляем peak_memory в результат для отображения в команде
            $result['peak_memory'] = round(memory_get_peak_usage(true) / 1024 / 1024, 2) . 'MB';

            return $result;
        } catch (\Exception $e) {
            Log::error('[FetchEmailNewsAction] Критическая ошибка', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Обработать одно письмо
     *
     * @param array $email Данные письма
     * @param Folder $folder IMAP папка
     * @return array Результат обработки
     */
    private function processEmail(array $email, Folder $folder): array
    {
        Log::info('[FetchEmailNewsAction:processEmail] Обработка письма', [
            'subject' => $email['subject'] ?? 'unknown',
            'from' => $email['from_email'] ?? 'unknown',
            'uid' => $email['uid'] ?? 'unknown',
        ]);

        try {
            // SAFETY: Проверка наличия валидного IMAP message объекта
            if (!isset($email['message']) || !is_object($email['message'])) {
                Log::warning('[FetchEmailNewsAction:processEmail] Отсутствует IMAP message объект', [
                    'subject' => $email['subject'] ?? 'unknown',
                ]);

                return [
                    'success' => false,
                    'error' => 'Invalid IMAP message object',
                ];
            }

            // OPTIMIZATION: Загружаем вложения (тело загружается лениво)
            $attachments = $this->downloadAttachmentsTask->run($email['message']);

            // Проверяем, есть ли DOC/DOCX файл
            $hasDocument = collect($attachments)->contains(fn($att) => $att->isDocument());

            if (!$hasDocument) {
                Log::warning('[FetchEmailNewsAction:processEmail] Нет DOC/DOCX файла во вложениях', [
                    'subject' => $email['subject'] ?? 'unknown',
                ]);

                return [
                    'success' => false,
                    'error' => 'Нет DOC/DOCX файла для извлечения текста',
                ];
            }

            // Конвертируем вложения в UploadedFile
            $uploadedFiles = $this->convertToUploadedFiles($attachments);

            // Обрабатываем через существующий ProcessMixedFilesAction
            $postResult = $this->processMixedFilesAction->run($uploadedFiles);

            // Помечаем письмо как прочитанное
            $this->markEmail($email['message'], $folder);

            Log::info('[FetchEmailNewsAction:processEmail] Письмо успешно обработано', [
                'subject' => $email['subject'] ?? 'unknown',
                'post_id' => $postResult['post']->id,
            ]);

            return [
                'success' => true,
                'post' => $postResult['post'],
                'attachments_count' => count($attachments),
            ];
        } catch (\Exception $e) {
            Log::error('[FetchEmailNewsAction:processEmail] Ошибка обработки письма', [
                'subject' => $email['subject'] ?? 'unknown',
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Конвертировать EmailAttachmentData в UploadedFile
     *
     * @param array<EmailAttachmentData> $attachments
     * @return \Illuminate\Support\Collection<UploadedFile>
     */
    private function convertToUploadedFiles(array $attachments): \Illuminate\Support\Collection
    {
        $uploadedFiles = [];

        foreach ($attachments as $attachment) {
            $fullPath = storage_path('app/' . $attachment->path);

            if (!file_exists($fullPath)) {
                Log::warning('[FetchEmailNewsAction:convertToUploadedFiles] Файл не найден', [
                    'path' => $attachment->path,
                ]);
                continue;
            }

            // Создаём UploadedFile из сохранённого файла
            $uploadedFile = new UploadedFile(
                $fullPath,
                $attachment->filename,
                $attachment->mimeType,
                null,
                true // test = false (файл валиден)
            );

            $uploadedFiles[] = $uploadedFile;
        }

        Log::info('[FetchEmailNewsAction:convertToUploadedFiles] Конвертировано файлов', [
            'count' => count($uploadedFiles),
        ]);

        return collect($uploadedFiles);
    }

    /**
     * Пометить письмо как прочитанное (и возможно переместить)
     *
     * @param object $message IMAP сообщение
     * @param Folder $folder Текущая папка
     */
    private function markEmail(object $message, Folder $folder): void
    {
        $moveToFolder = config('email-news.move_to_folder');

        if ($moveToFolder) {
            $this->markEmailAsReadTask->markAndMove($message, $moveToFolder);
        } elseif (config('email-news.mark_as_read', true)) {
            $this->markEmailAsReadTask->run($message);
        }
    }

    /**
     * OPTIMIZATION: Проверка использования памяти и принудительная сборка мусора
     *
     * Предотвращает переполнение памяти при обработке больших писем.
     */
    private function collectGarbageIfNeeded(): void
    {
        $currentMemory = memory_get_usage(true);

        if ($currentMemory > self::MEMORY_THRESHOLD) {
            $memoryBefore = round($currentMemory / 1024 / 1024, 2);

            // Принудительная сборка мусора
            gc_collect_cycles();

            $memoryAfter = round(memory_get_usage(true) / 1024 / 1024, 2);
            $freed = round(($memoryBefore - $memoryAfter), 2);

            Log::info('[FetchEmailNewsAction] Сборка мусора', [
                'memory_before' => $memoryBefore . 'MB',
                'memory_after' => $memoryAfter . 'MB',
                'freed' => $freed . 'MB',
            ]);
        }
    }
}
