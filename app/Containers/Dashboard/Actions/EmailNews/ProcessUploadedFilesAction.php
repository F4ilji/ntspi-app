<?php

namespace App\Containers\Dashboard\Actions\EmailNews;

use App\Containers\Article\Models\Category;
use App\Containers\Article\Models\Post;
use App\Containers\Dashboard\Tasks\AI\CallAiServiceTask;
use App\Containers\Dashboard\Tasks\Files\CompressImageTask;
use App\Containers\Dashboard\Tasks\Posts\CreatePostFromAiDataTask;
use App\Containers\Dashboard\Tasks\AI\ExtractTextFromDocumentTask;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class ProcessUploadedFilesAction
{
    public function __construct(
        private readonly ExtractTextFromDocumentTask $extractTextFromDocumentTask,
        private readonly CallAiServiceTask $callAiServiceTask,
        private readonly CreatePostFromAiDataTask $createPostFromAiDataTask,
        private readonly CompressImageTask $compressImageTask,
    ) {}

    /**
     * Обрабатывает загруженные файлы и создаёт пост
     *
     * @param UploadedFile $document Основной документ
     * @param array $mediaFiles Массив медиафайлов
     * @return array Данные о созданном посте
     */
    public function run(UploadedFile $document, array $mediaFiles = []): array
    {
        // Извлекаем текст из DOCX
        $extractedText = $this->extractText($document);

        // Сохраняем документ
        $documentPath = $document->store('documents', 'local');

        // Сохраняем медиафайлы
        $mediaPaths = $this->saveMediaFiles($mediaFiles);

        // Обрабатываем прикреплённые файлы из media (DOCX, PDF и т.д.)
        $attachedFiles = $this->processAttachedFiles($mediaFiles);

        // Получаем категории
        $categories = Category::all();

        // Отправляем текст в AI
        $newsData = $this->callAiService($extractedText, $categories);

        if (!$newsData) {
            throw new \RuntimeException('Не удалось распознать данные через AI сервис');
        }

        // Создаём пост
        $post = $this->createPost($newsData, $documentPath, $mediaPaths, $attachedFiles);

        // Возвращаем данные для отображения
        return $this->prepareResponse($post, $newsData);
    }

    /**
     * Извлекает текст из документа
     */
    private function extractText(UploadedFile $document): ?string
    {
        return $this->extractTextFromDocumentTask->run($document);
    }

    /**
     * Сохраняет медиафайлы со сжатием
     */
    private function saveMediaFiles(array $mediaFiles): array
    {
        $paths = [];
        $compressionStats = ['total' => 0, 'compressed' => 0, 'saved_bytes' => 0];

        foreach ($mediaFiles as $file) {
            $compressionStats['total']++;

            // Если это изображение - сжимаем
            if ($this->compressImageTask->isImage($file)) {
                $result = $this->compressImageTask->run($file);

                if ($result['compressed']) {
                    $compressionStats['compressed']++;
                    $compressionStats['saved_bytes'] += $result['original_size'] - $result['size'];
                }

                // Сохраняем сжатый файл
                $path = $result['file']->store('media', 'public');

                // Очищаем временный файл
                if (file_exists($result['file']->getRealPath())) {
                    unlink($result['file']->getRealPath());
                }

                $paths[] = $path;
            } else {
                // Не изображения сохраняем как есть
                $path = $file->store('media', 'public');
                $paths[] = $path;
            }
        }

        Log::info('[ProcessUploadedFilesAction] Статистика сжатия', [
            'total_images' => $compressionStats['total'],
            'compressed' => $compressionStats['compressed'],
            'saved' => $this->formatFileSize($compressionStats['saved_bytes']),
        ]);

        return $paths;
    }

    /**
     * Обрабатывает прикреплённые файлы (для добавления в контент)
     */
    private function processAttachedFiles(array $mediaFiles): array
    {
        $attachedFiles = [];
        $fileExtensions = ['doc', 'docx', 'pdf', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'rar'];

        foreach ($mediaFiles as $file) {
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
     * Вызывает AI сервис для распознавания данных
     */
    private function callAiService(?string $text, $categories): ?array
    {
        if (!$text) {
            return null;
        }

        return $this->callAiServiceTask->run($text, $categories);
    }

    /**
     * Создаёт пост из данных
     */
    private function createPost(array $newsData, ?string $documentPath, array $mediaPaths, array $attachedFiles): Post
    {
        return $this->createPostFromAiDataTask->run($newsData, $documentPath, $mediaPaths, $attachedFiles);
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
