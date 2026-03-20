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
        ]);

        // Извлекаем текст из основного файла (поддерживает и .doc, и .docx)
        $extractedText = $this->extractTextFromDocumentTask->run($mainFile);

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
}
