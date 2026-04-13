<?php

namespace App\Containers\Dashboard\Actions\ContentBuilder;

use App\Containers\Dashboard\Tasks\Files\UploadFileTask;
use Illuminate\Http\UploadedFile;

class UploadContentBuilderFilesAction
{
    public function __construct(
        private readonly UploadFileTask $uploadFileTask,
    ) {}

    /**
     * Загружает массив файлов и возвращает метаданные
     *
     * @param UploadedFile[] $files Массив загруженных файлов
     * @return array[] Массив метаданных файлов ['path', 'url', 'original_name', 'extension', 'size']
     */
    public function run(array $files): array
    {
        $results = [];

        foreach ($files as $file) {
            if (!$file instanceof UploadedFile) {
                continue;
            }

            $uploadResult = $this->uploadFileTask->run($file);

            $results[] = [
                'path' => $uploadResult['path'],
                'url' => $uploadResult['url'],
                'title' => $uploadResult['original_name'],
                'expansion' => strtolower($file->getClientOriginalExtension()),
                'size' => $this->formatFileSize($file->getSize()),
            ];
        }

        return $results;
    }

    /**
     * Форматирует размер файла в читаемый вид
     */
    private function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
