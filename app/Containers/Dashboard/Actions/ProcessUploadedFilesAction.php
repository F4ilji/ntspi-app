<?php

namespace App\Containers\Dashboard\Actions;

use App\Containers\Article\Models\Category;
use App\Containers\Article\Models\Post;
use App\Containers\Dashboard\Tasks\CallAiServiceTask;
use App\Containers\Dashboard\Tasks\CreatePostFromAiDataTask;
use App\Containers\Dashboard\Tasks\ExtractTextFromDocumentTask;
use Illuminate\Http\UploadedFile;

class ProcessUploadedFilesAction
{
    public function __construct(
        private readonly ExtractTextFromDocumentTask $extractTextFromDocumentTask,
        private readonly CallAiServiceTask $callAiServiceTask,
        private readonly CreatePostFromAiDataTask $createPostFromAiDataTask,
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
     * Сохраняет медиафайлы
     */
    private function saveMediaFiles(array $mediaFiles): array
    {
        return collect($mediaFiles)
            ->map(fn($file) => $file->store('media', 'public'))
            ->toArray();
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
