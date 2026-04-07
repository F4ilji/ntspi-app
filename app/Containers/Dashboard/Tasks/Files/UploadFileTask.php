<?php

namespace App\Containers\Dashboard\Tasks\Files;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadFileTask
{
    /**
     * Загружает файл на публичный диск и возвращает путь
     *
     * @param UploadedFile $file Файл для загрузки
     * @return array ['path' => путь в storage, 'url' => публичный URL, 'original_name' => оригинальное имя]
     */
    public function run(UploadedFile $file): array
    {
        $originalFileNameWithExtension = $file->getClientOriginalName();
        $originalFileName = pathinfo($originalFileNameWithExtension, PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();

        $sluggedFileName = Str::slug($originalFileName);
        $uniqueFileName = $sluggedFileName . '-' . md5(uniqid(rand(), true)) . '.' . $extension;
        
        $path = $file->storeAs('files', $uniqueFileName, 'public');
        $url = Storage::url($path);

        return [
            'path' => $path,
            'url' => $url,
            'original_name' => $originalFileNameWithExtension,
        ];
    }
}
