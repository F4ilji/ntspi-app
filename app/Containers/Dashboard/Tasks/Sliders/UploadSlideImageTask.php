<?php

namespace App\Containers\Dashboard\Tasks\Sliders;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class UploadSlideImageTask
{
    public function run(UploadedFile $file): array
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('slides', $filename, 'public');
        
        // Сохраняем только путь файла (как Filament)
        return [
            'url' => $path,
            'path' => $path,
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime' => $file->getMimeType(),
        ];
    }
}
