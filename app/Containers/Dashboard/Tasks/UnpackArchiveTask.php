<?php

namespace App\Containers\Dashboard\Tasks;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class UnpackArchiveTask
{
    /**
     * Распаковывает архив и возвращает коллекцию файлов
     *
     * @param UploadedFile $archive Архив для распаковки
     * @param string $extractPath Путь для извлечения файлов
     * @return array Массив с файлами и путем к директории
     * @throws \Exception
     */
    public function run(UploadedFile $archive, string $extractPath = null): array
    {
        $extension = strtolower($archive->getClientOriginalExtension());
        
        // Поддерживаем только zip для начала
        if ($extension !== 'zip') {
            throw new \Exception("Неподдерживаемый формат архива: {$extension}. Поддерживается только ZIP.");
        }

        $extractPath = $extractPath ?? storage_path('app/temp/unpacked_' . uniqid());
        
        // Создаем директорию если не существует
        if (!file_exists($extractPath)) {
            mkdir($extractPath, 0755, true);
        }

        Log::info('[UnpackArchiveTask] Начало распаковки архива', [
            'file' => $archive->getClientOriginalName(),
            'size' => $archive->getSize(),
            'extract_path' => $extractPath,
        ]);

        try {
            $zip = new \ZipArchive();
            
            $openResult = $zip->open($archive->getRealPath());
            if ($openResult !== true) {
                throw new \Exception('Не удалось открыть архив. Код ошибки: ' . $openResult);
            }

            // Извлекаем все файлы с переименованием (кириллица → транслит)
            Log::info('[UnpackArchiveTask] Количество файлов в архиве', ['numFiles' => $zip->numFiles]);
            
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $fileInfo = $zip->statIndex($i);
                $originalName = $fileInfo['name'];
                
                Log::info('[UnpackArchiveTask] Обработка файла в архиве', [
                    'index' => $i,
                    'original_name' => $originalName,
                    'is_dir' => substr($originalName, -1) === '/',
                ]);
                
                // Пропускаем директории
                if (substr($originalName, -1) === '/') {
                    continue;
                }
                
                // Преобразуем имя файла (кириллица → транслит)
                $safeName = $this->transliterateFilename($originalName);
                
                Log::info('[UnpackArchiveTask] Транслитерация имени', [
                    'original' => $originalName,
                    'safe' => $safeName,
                ]);
                
                // Определяем директорию для файла
                $dirname = dirname($safeName);
                $targetDir = $extractPath;
                if ($dirname !== '.' && $dirname !== '/') {
                    $targetDir = $extractPath . '/' . $dirname;
                    // Создаем поддиректории если нужно
                    if (!file_exists($targetDir)) {
                        mkdir($targetDir, 0755, true);
                    }
                }
                
                // Получаем содержимое файла из архива
                $content = $zip->getFromIndex($i);
                
                if ($content === false) {
                    Log::error('[UnpackArchiveTask] Не удалось прочитать содержимое файла', [
                        'name' => $originalName,
                    ]);
                    continue;
                }
                
                // Сохраняем файл с безопасным именем
                $targetPath = $targetDir . '/' . basename($safeName);
                $writeResult = file_put_contents($targetPath, $content);
                
                if ($writeResult === false) {
                    Log::error('[UnpackArchiveTask] Не удалось записать файл', [
                        'path' => $targetPath,
                    ]);
                    continue;
                }
                
                Log::info('[UnpackArchiveTask] Файл извлечен', [
                    'path' => $targetPath,
                    'size' => $writeResult,
                ]);
            }
            
            $zip->close();

            Log::info('[UnpackArchiveTask] Архив распакован', [
                'files_count' => $this->countFilesInDirectory($extractPath),
            ]);

            // Собираем все файлы из распакованной директории
            $files = $this->collectFilesFromDirectory($extractPath);

            Log::info('[UnpackArchiveTask] Файлы собраны', [
                'files' => $files->map(fn($f) => $f->getClientOriginalName())->toArray(),
            ]);

            // Возвращаем файлы вместе с путем к директории для последующей очистки
            return ['files' => $files, 'extract_path' => $extractPath];
        } catch (\Exception $e) {
            Log::error('[UnpackArchiveTask] Ошибка при распаковке архива', [
                'error' => $e->getMessage(),
                'file' => $archive->getClientOriginalName(),
            ]);
            
            // Очищаем временную директорию при ошибке
            if (file_exists($extractPath)) {
                $this->cleanupDirectory($extractPath);
            }
            
            throw new \Exception('Ошибка при распаковке архива: ' . $e->getMessage());
        }
    }

    /**
     * Преобразует имя файла (транслитерация кириллицы)
     */
    private function transliterateFilename(string $filename): string
    {
        $translit = [
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
            'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
            'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
            'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
            'ш' => 'sh', 'щ' => 'sch', 'ъ' => '', 'ы' => 'y', 'ь' => '',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
            'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D',
            'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh', 'З' => 'Z', 'И' => 'I',
            'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N',
            'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T',
            'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C', 'Ч' => 'Ch',
            'Ш' => 'Sh', 'Щ' => 'Sch', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '',
            'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
        ];

        $pathinfo = pathinfo($filename);
        $dirname = $pathinfo['dirname'] ?? '';
        $filename = $pathinfo['filename'];
        $extension = $pathinfo['extension'] ?? '';

        // Транслитерируем имя файла
        $transliterated = strtr($filename, $translit);
        
        // Заменяем пробелы и спецсимволы на подчеркивания
        $transliterated = preg_replace('/[^A-Za-z0-9_\-]/', '_', $transliterated);
        
        // Собираем обратно
        $result = $dirname !== '.' ? $dirname . '/' . $transliterated : $transliterated;
        if ($extension) {
            $result .= '.' . $extension;
        }

        return $result;
    }

    /**
     * Собирает все файлы из директории рекурсивно
     */
    private function collectFilesFromDirectory(string $path): Collection
    {
        $files = collect();

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                // Определяем MIME-тип для правильного расширения
                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->file($file->getPathname());
                
                // Получаем расширение по MIME-типу
                $extension = $this->getExtensionFromMimeType($mimeType);
                
                // Если не удалось определить по MIME, используем расширение из имени
                if (!$extension) {
                    $extension = pathinfo($file->getFilename(), PATHINFO_EXTENSION);
                }

                $uploadedFile = new UploadedFile(
                    $file->getPathname(),
                    $file->getFilename(),
                    $extension,
                    null,
                    true
                );
                $files->push($uploadedFile);
            }
        }

        return $files;
    }

    /**
     * Возвращает расширение по MIME-типу
     */
    private function getExtensionFromMimeType(string $mimeType): ?string
    {
        $map = [
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/pdf' => 'pdf',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            'image/bmp' => 'bmp',
            'application/zip' => 'zip',
        ];

        return $map[$mimeType] ?? null;
    }

    /**
     * Подсчитывает количество файлов в директории
     */
    private function countFilesInDirectory(string $path): int
    {
        $count = 0;
        
        if (!is_dir($path)) {
            return 0;
        }
        
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Очищает временную директорию
     */
    private function cleanupDirectory(string $path): void
    {
        if (!file_exists($path) || !is_dir($path)) {
            return;
        }

        try {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    @unlink($file->getPathname());
                } elseif ($file->isDir()) {
                    @rmdir($file->getPathname());
                }
            }

            @rmdir($path);
        } catch (\Exception $e) {
            Log::warning('[UnpackArchiveTask] Не удалось очистить временную директорию', [
                'error' => $e->getMessage(),
                'path' => $path,
            ]);
        }
    }
}
