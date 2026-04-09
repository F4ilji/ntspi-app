<?php

namespace App\Containers\Dashboard\Actions\Posts;

use App\Containers\Dashboard\Tasks\Files\UploadFileTask;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UploadPostImagesAction
{
    public function __construct(
        private readonly UploadFileTask $uploadFileTask,
    ) {}

    /**
     * Загружает массив изображений и возвращает пути
     *
     * @param UploadedFile[] $files Массив загруженных файлов
     * @return string[] Массив путей к файлам
     */
    public function run(array $files): array
    {
        $paths = [];

        foreach ($files as $file) {
            if (!$file instanceof UploadedFile) {
                continue;
            }

            // Проверяем что это изображение
            if (!str_starts_with($file->getMimeType(), 'image/')) {
                continue;
            }

            $result = $this->uploadFileTask->run($file);
            $paths[] = $result['path'];
        }

        return $paths;
    }
}
