<?php

namespace App\Containers\Dashboard\Actions;

use App\Containers\Article\Models\Category;
use App\Containers\Article\Models\Post;
use App\Containers\Dashboard\Tasks\CallAiServiceForFileSelectionTask;
use App\Containers\Dashboard\Tasks\CallAiServiceTask;
use App\Containers\Dashboard\Tasks\CreatePostFromAiDataTask;
use App\Containers\Dashboard\Tasks\ExtractTextFromDocumentTask;
use App\Containers\Dashboard\Tasks\ExtractTextFragmentTask;
use App\Containers\Dashboard\Tasks\FindMainNewsFileTask;
use App\Containers\Dashboard\Tasks\UnpackArchiveTask;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ProcessMixedFilesAction
{
    private array $extractPathsToCleanup = [];

    public function __construct(
        private readonly FindMainNewsFileTask $findMainNewsFileTask,
        private readonly ExtractTextFromDocumentTask $extractTextFromDocumentTask,
        private readonly CallAiServiceTask $callAiServiceTask,
        private readonly CreatePostFromAiDataTask $createPostFromAiDataTask,
        private readonly UnpackArchiveTask $unpackArchiveTask,
    ) {}

    /**
     * Обрабатывает смешанные файлы и создает пост
     *
     * @param Collection<UploadedFile> $files Все загруженные файлы
     * @return array Данные о созданном посте
     */
    public function run(Collection $files): array
    {
        Log::info('[ProcessMixedFilesAction] Начало обработки файлов', [
            'files_count' => $files->count(),
            'files' => $files->map(fn($f) => $f->getClientOriginalName())->toArray(),
        ]);

        // Проверяем, есть ли архивы и нужно ли их распаковывать
        $files = $this->processArchives($files);

        Log::info('[ProcessMixedFilesAction] Файлы после обработки архивов', [
            'files_count' => $files->count(),
            'files' => $files->map(fn($f) => $f->getClientOriginalName())->toArray(),
        ]);

        // После распаковки проверяем, что есть DOC/DOCX файл
        $hasDocOrDocx = $files->contains(function ($file) {
            $ext = strtolower($file->getClientOriginalExtension());
            return in_array($ext, ['doc', 'docx']);
        });

        if (!$hasDocOrDocx) {
            Log::error('[ProcessMixedFilesAction] После распаковки не найден DOC/DOCX файл');
            throw new \RuntimeException('В архиве не найден DOC или DOCX файл для извлечения текста новости.');
        }

        // Находим основной файл с текстом новости
        $mainFile = $this->findMainNewsFileTask->run($files);

        if (!$mainFile) {
            Log::error('[ProcessMixedFilesAction] Не найден файл для извлечения текста');
            throw new \RuntimeException('Не найден DOC/DOCX файл для извлечения текста');
        }

        Log::info('[ProcessMixedFilesAction] Основной файл найден', [
            'file' => $mainFile->getClientOriginalName(),
            'extension' => $mainFile->getClientOriginalExtension(),
            'real_path' => $mainFile->getRealPath(),
            'exists' => file_exists($mainFile->getRealPath()),
        ]);

        // Извлекаем текст из основного файла (поддерживает и .doc, и .docx)
        Log::info('[ProcessMixedFilesAction] Начало извлечения текста из документа');
        try {
            $extractedText = $this->extractTextFromDocumentTask->run($mainFile);
            Log::info('[ProcessMixedFilesAction] Текст извлечен', [
                'text_length' => strlen($extractedText ?? ''),
                'text_preview' => substr($extractedText ?? '', 0, 100),
            ]);
        } catch (\Exception $e) {
            Log::error('[ProcessMixedFilesAction] Ошибка при извлечении текста', [
                'error' => $e->getMessage(),
                'file' => $mainFile->getClientOriginalName(),
            ]);
            throw $e;
        }

        Log::info('[ProcessMixedFilesAction] Результат извлечения текста', [
            'text_length' => strlen($extractedText ?? ''),
            'has_text' => !empty($extractedText),
        ]);

        if (empty($extractedText)) {
            Log::warning('[ProcessMixedFilesAction] Пустой текст после извлечения', [
                'file' => $mainFile->getClientOriginalName(),
            ]);
        }

        // Сохраняем все файлы
        ['documentPath' => $documentPath, 'mediaPaths' => $mediaPaths] = $this->saveFiles($files, $mainFile);

        // Обрабатываем прикреплённые файлы из media
        $attachedFiles = $this->processAttachedFiles($files, $mainFile);

        // Получаем категории
        $categories = Category::all();

        // Отправляем текст в AI
        $newsData = $this->callAiServiceTask->run($extractedText, $categories);

        if (!$newsData) {
            throw new \RuntimeException('Не удалось распознать данные через AI сервис');
        }

        // Создаём пост
        $post = $this->createPostFromAiDataTask->run($newsData, $documentPath, $mediaPaths, $attachedFiles);

        // Очищаем временные директории после успешной обработки
        $this->cleanupExtractPaths();

        // Возвращаем данные для отображения
        return $this->prepareResponse($post, $newsData);
    }

    /**
     * Сохраняет все файлы
     */
    private function saveFiles(Collection $files, UploadedFile $mainFile): array
    {
        // Сохраняем основной документ
        $documentPath = $mainFile->store('documents', 'local');

        // Сохраняем остальные файлы как медиа
        $mediaPaths = $files
            ->filter(fn($file) => $file !== $mainFile)
            ->map(fn($file) => $file->store('media', 'public'))
            ->toArray();

        return [
            'documentPath' => $documentPath,
            'mediaPaths' => $mediaPaths,
        ];
    }

    /**
     * Обрабатывает архивы: распаковывает, если загружены только архивы
     */
    private function processArchives(Collection $files): Collection
    {
        $archiveExtensions = ['zip'];
        
        // Убедимся, что директория для распаковки существует
        $tempPath = storage_path('app/temp');
        if (!file_exists($tempPath)) {
            mkdir($tempPath, 0755, true);
        }
        
        // Разделяем файлы на архивы и остальные
        $archives = $files->filter(fn($file) => 
            in_array(strtolower($file->getClientOriginalExtension()), $archiveExtensions)
        );
        
        $nonArchives = $files->filter(fn($file) => 
            !in_array(strtolower($file->getClientOriginalExtension()), $archiveExtensions)
        );

        // Если есть только архивы (нет других файлов) — распаковываем
        if ($archives->isNotEmpty() && $nonArchives->isEmpty()) {
            Log::info('[ProcessMixedFilesAction] Обнаружены только архивы, начинаем распаковку', [
                'archives_count' => $archives->count(),
            ]);

            $unpackedFiles = collect();
            $extractPaths = [];

            foreach ($archives as $archive) {
                try {
                    $result = $this->unpackArchiveTask->run($archive);
                    $extractedFiles = $result['files'];
                    $extractPaths[] = $result['extract_path'];
                    
                    $unpackedFiles = $unpackedFiles->merge($extractedFiles);
                    
                    Log::info('[ProcessMixedFilesAction] Архив распакован', [
                        'archive' => $archive->getClientOriginalName(),
                        'extracted_count' => $extractedFiles->count(),
                    ]);
                } catch (\Exception $e) {
                    Log::error('[ProcessMixedFilesAction] Ошибка при распаковке архива', [
                        'archive' => $archive->getClientOriginalName(),
                        'error' => $e->getMessage(),
                    ]);
                    throw $e;
                }
            }

            // Сохраняем пути для очистки после обработки
            $this->extractPathsToCleanup = $extractPaths;

            return $unpackedFiles;
        }

        // Если есть не-архивные файлы — возвращаем все файлы как есть
        // (архивы будут обработаны как обычные медиа-файлы)
        Log::info('[ProcessMixedFilesAction] Обнаружены смешанные файлы, архивы не распаковываем', [
            'archives_count' => $archives->count(),
            'non_archives_count' => $nonArchives->count(),
        ]);

        return $files;
    }

    /**
     * Обрабатывает прикреплённые файлы (для добавления в контент)
     */
    private function processAttachedFiles(Collection $files, UploadedFile $mainFile): array
    {
        $attachedFiles = [];
        $fileExtensions = ['doc', 'docx', 'pdf', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'rar'];

        foreach ($files as $file) {
            // Пропускаем основной файл
            if ($file === $mainFile) {
                continue;
            }

            $extension = strtolower($file->getClientOriginalExtension());

            // Пропускаем изображения
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp'])) {
                continue;
            }

            // Обрабатываем только файлы-вложения
            if (!in_array($extension, $fileExtensions)) {
                continue;
            }

            $savedPath = $file->store('media/attachments', 'public');

            $attachedFiles[] = [
                'expansion' => $extension,
                'size' => $this->formatFileSize($file->getSize()),
                'time_added' => time(),
                'title' => $file->getClientOriginalName(),
                'path' => $savedPath,
            ];
        }

        return $attachedFiles;
    }

    /**
     * Подготавливает ответ для возврата
     */
    private function prepareResponse(Post $post, array $newsData): array
    {
        $post->load('category');

        return [
            'post' => $post,
            'newsData' => $newsData,
            'preview_url' => $post->preview ? asset('storage/' . $post->preview) : null,
            'images_urls' => $post->images
                ? array_map(fn($img) => asset('storage/' . $img), $post->images)
                : [],
        ];
    }

    /**
     * Форматирует размер файла
     */
    private function formatFileSize(int $bytes): string
    {
        $units = ['б', 'КиБ', 'МиБ', 'ГиБ'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Очищает временные директории после распаковки
     */
    private function cleanupExtractPaths(): void
    {
        foreach ($this->extractPathsToCleanup as $path) {
            if (file_exists($path)) {
                $this->cleanupDirectory($path);
            }
        }
        $this->extractPathsToCleanup = [];
    }

    /**
     * Очищает директорию рекурсивно
     */
    private function cleanupDirectory(string $path): void
    {
        if (!file_exists($path) || !is_dir($path)) {
            return;
        }

        try {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    @unlink($file->getPathname());
                } elseif ($file->isDir()) {
                    @rmdir($file->getPathname());
                }
            }

            @rmdir($path);
        } catch (\Exception $e) {
            Log::warning('[ProcessMixedFilesAction] Не удалось очистить временную директорию', [
                'error' => $e->getMessage(),
                'path' => $path,
            ]);
        }
    }
}
