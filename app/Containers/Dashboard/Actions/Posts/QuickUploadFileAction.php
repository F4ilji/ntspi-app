<?php

namespace App\Containers\Dashboard\Actions\Posts;

use App\Containers\Dashboard\Tasks\Files\UploadFileTask;
use Illuminate\Http\UploadedFile;

class QuickUploadFileAction
{
    public function __construct(
        private readonly UploadFileTask $uploadFileTask,
    ) {}

    /**
     * Загружает файл и возвращает данные о нём
     *
     * @param UploadedFile $file Файл для загрузки
     * @return array Данные о загруженном файле
     */
    public function run(UploadedFile $file): array
    {
        return $this->uploadFileTask->run($file);
    }
}
