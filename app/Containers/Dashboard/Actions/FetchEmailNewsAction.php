<?php

namespace App\Containers\Dashboard\Actions;

use App\Containers\Dashboard\Data\EmailAttachmentData;
use App\Containers\Dashboard\Exceptions\EmailFetchException;
use App\Containers\Dashboard\Tasks\ConnectToImapTask;
use App\Containers\Dashboard\Tasks\DownloadAttachmentsTask;
use App\Containers\Dashboard\Tasks\FetchUnreadEmailsTask;
use App\Containers\Dashboard\Tasks\FilterBySenderTask;
use App\Containers\Dashboard\Tasks\MarkEmailAsReadTask;
use App\Containers\Dashboard\Actions\ProcessMixedFilesAction;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Webklex\PHPIMAP\Client;
use Webklex\PHPIMAP\Folder;

/**
 * Оркестрация процесса получения новостей из Email
 */
class FetchEmailNewsAction
{
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
        ];

        try {
            // Подключаемся к IMAP
            $client = $this->connectToImapTask->run();

            // Получаем папку
            $folder = $this->connectToImapTask->getFolder(
                $client,
                config('email-news.folder', 'INBOX')
            );

            // Получаем непрочитанные письма
            $emails = $this->fetchUnreadEmailsTask->run($folder);

            if (empty($emails)) {
                Log::info('[FetchEmailNewsAction] Нет непрочитанных писем');
                return $result;
            }

            // Фильтруем по отправителю
            $filteredEmails = $this->filterBySenderTask->run($emails);

            $result['skipped_emails'] = count($emails) - count($filteredEmails);

            // Обрабатываем каждое письмо
            foreach ($filteredEmails as $email) {
                $emailResult = $this->processEmail($email, $folder);

                if ($emailResult['success']) {
                    $result['created_posts']++;
                    $result['posts'][] = $emailResult['post'];
                } else {
                    $result['errors'][] = [
                        'email_subject' => $email['subject'],
                        'error' => $emailResult['error'],
                    ];
                }

                $result['processed_emails']++;
            }

            Log::info('[FetchEmailNewsAction] Завершено', [
                'processed' => $result['processed_emails'],
                'created_posts' => $result['created_posts'],
                'skipped' => $result['skipped_emails'],
                'errors_count' => count($result['errors']),
            ]);

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
            'subject' => $email['subject'],
            'from' => $email['from_email'],
        ]);

        try {
            // Скачиваем вложения
            $attachments = $this->downloadAttachmentsTask->run($email['message']);

            // Проверяем, есть ли DOC/DOCX файл
            $hasDocument = collect($attachments)->contains(fn($att) => $att->isDocument());

            if (!$hasDocument) {
                Log::warning('[FetchEmailNewsAction:processEmail] Нет DOC/DOCX файла во вложениях', [
                    'subject' => $email['subject'],
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
                'subject' => $email['subject'],
                'post_id' => $postResult['post']->id,
            ]);

            return [
                'success' => true,
                'post' => $postResult['post'],
                'attachments_count' => count($attachments),
            ];
        } catch (\Exception $e) {
            Log::error('[FetchEmailNewsAction:processEmail] Ошибка обработки письма', [
                'subject' => $email['subject'],
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
}
