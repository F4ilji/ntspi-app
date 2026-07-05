<?php

namespace App\Containers\Dashboard\Tasks\AI;

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
        Log::channel('ai')->info('Начало конвертации', [
            'file' => $file->getClientOriginalName(),
            'extension' => $file->getClientOriginalExtension(),
        ]);

        if (strtolower($file->getClientOriginalExtension()) !== 'doc') {
            Log::channel('ai')->warning('Неверное расширение', ['extension' => $file->getClientOriginalExtension()]);
            return null;
        }

        // Сохраняем исходный файл
        $originalPath = $file->storeAs('temp/conversion', 'original_' . time() . '_' . Str::random(10) . '.doc', 'local');
        $absoluteOriginalPath = Storage::disk('local')->path($originalPath);

        if (!file_exists($absoluteOriginalPath)) {
            Log::channel('ai')->error('Файл не сохранен', ['path' => $absoluteOriginalPath]);
            return null;
        }

        $outputDir = dirname($absoluteOriginalPath);

        // Создаем уникальный путь для профиля LibreOffice в /tmp, чтобы избежать ошибок прав в /var/www
        $userProfileDir = '/tmp/libreoffice_profile_' . uniqid();

        // Формируем команду с изоляцией профиля и установкой HOME
        // -env:UserInstallation указывает LibreOffice использовать временную папку вместо системной .cache
        $command = sprintf(
            'export HOME=/tmp && libreoffice -env:UserInstallation=file://%s --headless --convert-to docx --outdir %s %s 2>&1',
            escapeshellarg($userProfileDir),
            escapeshellarg($outputDir),
            escapeshellarg($absoluteOriginalPath)
        );

        Log::channel('ai')->info('Выполнение команды', ['command' => $command]);

        $output = shell_exec($command);

        // Ожидаемый путь результата
        $docxPath = preg_replace('/\.doc$/i', '.docx', $absoluteOriginalPath);

        // Удаляем временный профиль LibreOffice сразу после выполнения
        if (is_dir($userProfileDir)) {
            shell_exec('rm -rf ' . escapeshellarg($userProfileDir));
        }

        if (!file_exists($docxPath)) {
            Log::channel('ai')->error('Конвертация не удалась', [
                'file' => $file->getClientOriginalName(),
                'output' => $output,
                'expected_path' => $docxPath,
            ]);

            Storage::disk('local')->delete($originalPath);
            return null;
        }

        // Удаляем исходный .doc
        Storage::disk('local')->delete($originalPath);

        $relativeDocxPath = preg_replace('/\.doc$/i', '.docx', $originalPath);

        Log::channel('ai')->info('Конвертация успешна', ['result_path' => $relativeDocxPath]);

        return $relativeDocxPath;
    }

}
