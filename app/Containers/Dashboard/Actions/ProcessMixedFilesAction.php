<?php

namespace App\Containers\Dashboard\Actions;

use App\Containers\Article\Models\Category;
use App\Containers\Article\Models\Post;
use App\Containers\Dashboard\Tasks\CallAiServiceTask;
use App\Containers\Dashboard\Tasks\CompressImageTask;
use App\Containers\Dashboard\Tasks\CreatePostFromAiDataTask;
use App\Containers\Dashboard\Tasks\ExtractTextFromDocumentTask;
use App\Containers\Dashboard\Tasks\FindMainNewsFileTask;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ProcessMixedFilesAction
{
    public function __construct(
        private readonly FindMainNewsFileTask $findMainNewsFileTask,
        private readonly ExtractTextFromDocumentTask $extractTextFromDocumentTask,
        private readonly CallAiServiceTask $callAiServiceTask,
        private readonly CreatePostFromAiDataTask $createPostFromAiDataTask,
        private readonly CompressImageTask $compressImageTask,
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

        // Возвращаем данные для отображения
        return $this->prepareResponse($post, $newsData);
    }

    /**
     * Сохраняет все файлы
     */
    private function saveFiles(Collection $files, UploadedFile $mainFile): array
    {
        Log::info('[ProcessMixedFilesAction:saveFiles] Начало сохранения файлов', [
            'total_files' => $files->count(),
        ]);

        // Сохраняем основной документ
        $documentPath = $mainFile->store('documents', 'local');

        // Сжимаем и сохраняем остальные файлы как медиа
        $mediaPaths = [];
        $compressionStats = ['total' => 0, 'compressed' => 0, 'saved_bytes' => 0];

        foreach ($files as $file) {
            // Пропускаем основной файл
            if ($file === $mainFile) {
                continue;
            }

            $compressionStats['total']++;

            // Если это изображение - сжимаем
            if ($this->compressImageTask->isImage($file)) {
                Log::info('[ProcessMixedFilesAction:saveFiles] Обработка изображения', [
                    'file' => $file->getClientOriginalName(),
                ]);

                $result = $this->compressImageTask->run($file);

                if ($result['compressed']) {
                    $compressionStats['compressed']++;
                    $compressionStats['saved_bytes'] += $result['original_size'] - $result['size'];

                    Log::info('[ProcessMixedFilesAction:saveFiles] Изображение сжато', [
                        'file' => $file->getClientOriginalName(),
                        'original_size' => $this->formatFileSize($result['original_size']),
                        'compressed_size' => $this->formatFileSize($result['size']),
                        'ratio' => $result['compression_ratio'] . '%',
                    ]);
                }

                // Сохраняем сжатый файл
                $path = $result['file']->store('media', 'public');

                // Очищаем временный файл
                if (file_exists($result['file']->getRealPath())) {
                    unlink($result['file']->getRealPath());
                }

                $mediaPaths[] = $path;
            } else {
                // Не изображения сохраняем как есть
                $path = $file->store('media', 'public');
                $mediaPaths[] = $path;
            }
        }

        Log::info('[ProcessMixedFilesAction:saveFiles] Статистика сжатия', [
            'total_images' => $compressionStats['total'],
            'compressed' => $compressionStats['compressed'],
            'saved' => $this->formatFileSize($compressionStats['saved_bytes']),
            'saved_bytes' => $compressionStats['saved_bytes'],
        ]);

        return [
            'documentPath' => $documentPath,
            'mediaPaths' => $mediaPaths,
            'compressionStats' => $compressionStats,
        ];
    }

    /**
     * Обрабатывает прикреплённые файлы (для добавления в контент)
     */
    private function processAttachedFiles(Collection $files, UploadedFile $mainFile): array
    {
        $attachedFiles = [];
        $fileExtensions = ['doc', 'docx', 'pdf', 'xls', 'xlsx', 'ppt', 'pptx'];

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
}
