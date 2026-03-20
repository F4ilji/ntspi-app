<?php

namespace App\Containers\Dashboard\Tasks;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ConvertDocToDocxTask
{
    /**
     * Конвертирует .doc файл в .docx с помощью LibreOffice
     *
     * @param UploadedFile $file Исходный .doc файл
     * @return string|null Путь к конвертированному .docx файлу или null при ошибке
     */
    public function run(UploadedFile $file): ?string
    {
        Log::info('[ConvertDocToDocxTask] Начало конвертации', [
            'file' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'extension' => $file->getClientOriginalExtension(),
        ]);

        // Проверяем расширение
        if (strtolower($file->getClientOriginalExtension()) !== 'doc') {
            Log::warning('[ConvertDocToDocxTask] Неверное расширение', [
                'extension' => $file->getClientOriginalExtension(),
            ]);
            return null;
        }

        // Сохраняем исходный файл во временную директорию
        $originalPath = $file->storeAs('temp/conversion', 'original_' . time() . '_' . Str::random(10) . '.doc', 'local');
        $absoluteOriginalPath = Storage::disk('local')->path($originalPath);

        Log::info('[ConvertDocToDocxTask] Файл сохранен', [
            'original_path' => $originalPath,
            'absolute_path' => $absoluteOriginalPath,
            'file_exists' => file_exists($absoluteOriginalPath),
        ]);

        // Проверяем существование файла
        if (!file_exists($absoluteOriginalPath)) {
            Log::error('[ConvertDocToDocxTask] Файл не сохранен', [
                'path' => $absoluteOriginalPath,
            ]);
            return null;
        }

        // Директория для вывода
        $outputDir = dirname($absoluteOriginalPath);

        // Конвертируем через LibreOffice
        $command = sprintf(
            'libreoffice --headless --convert-to docx --outdir %s %s 2>&1',
            escapeshellarg($outputDir),
            escapeshellarg($absoluteOriginalPath)
        );

        Log::info('[ConvertDocToDocxTask] Выполнение команды', [
            'command' => $command,
        ]);

        $output = shell_exec($command);

        Log::info('[ConvertDocToDocxTask] Результат конвертации', [
            'output' => $output,
        ]);

        // Ищем конвертированный файл
        $docxPath = preg_replace('/\.doc$/i', '.docx', $absoluteOriginalPath);

        Log::info('[ConvertDocToDocxTask] Проверка результата', [
            'expected_path' => $docxPath,
            'file_exists' => file_exists($docxPath),
        ]);

        if (!file_exists($docxPath)) {
            // Логируем ошибку
            Log::error('[ConvertDocToDocxTask] Конвертация не удалась', [
                'file' => $file->getClientOriginalName(),
                'output' => $output,
                'expected_path' => $docxPath,
            ]);

            // Удаляем исходный файл
            Storage::disk('local')->delete($originalPath);

            return null;
        }

        // Удаляем исходный .doc файл
        Storage::disk('local')->delete($originalPath);

        // Возвращаем относительный путь к .docx файлу
        $relativeDocxPath = preg_replace('/\.doc$/i', '.docx', $originalPath);

        Log::info('[ConvertDocToDocxTask] Конвертация успешна', [
            'result_path' => $relativeDocxPath,
        ]);

        return $relativeDocxPath;
    }
}
