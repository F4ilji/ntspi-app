<?php

namespace App\Containers\Dashboard\Tasks;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;

class CompressImageTask
{
    /**
     * Максимальное качество сжатия WebP (0-100)
     */
    private const WEBP_QUALITY = 82;

    /**
     * Максимальное качество для JPEG (0-100)
     */
    private const JPEG_QUALITY = 85;

    /**
     * Максимальная ширина изображения (px)
     */
    private const MAX_WIDTH = 1920;

    /**
     * Максимальная высота изображения (px)
     */
    private const MAX_HEIGHT = 1920;

    /**
     * Порог размера файла для сжатия (в байтах) - 1MB
     */
    private const SIZE_THRESHOLD = 1048576;

    /**
     * Сжимает изображение и конвертирует в WebP
     *
     * @param UploadedFile $file Исходный файл изображения
     * @return array ['path' => путь к файлу, 'size' => размер в байтах, 'original_size' => исходный размер]
     */
    public function run(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());

        Log::info('[CompressImageTask] Начало обработки изображения', [
            'file' => $file->getClientOriginalName(),
            'extension' => $extension,
            'size' => $this->formatFileSize($file->getSize()),
            'size_bytes' => $file->getSize(),
        ]);

        // Если файл уже меньше порога и это WebP - пропускаем сжатие
        if ($file->getSize() <= self::SIZE_THRESHOLD && $extension === 'webp') {
            Log::info('[CompressImageTask] Файл уже оптимизирован, пропускаем', [
                'file' => $file->getClientOriginalName(),
            ]);

            return [
                'file' => $file,
                'size' => $file->getSize(),
                'original_size' => $file->getSize(),
                'compressed' => false,
            ];
        }

        // Создаём изображение через Intervention
        $img = Image::make($file->getRealPath());

        // Получаем исходные размеры
        $originalWidth = $img->width();
        $originalHeight = $img->height();

        Log::info('[CompressImageTask] Исходные размеры', [
            'width' => $originalWidth,
            'height' => $originalHeight,
        ]);

        // Ресайзим если больше максимальных размеров
        if ($originalWidth > self::MAX_WIDTH || $originalHeight > self::MAX_HEIGHT) {
            $img->resize(self::MAX_WIDTH, self::MAX_HEIGHT, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            Log::info('[CompressImageTask] Изображение ресайзнуто', [
                'new_width' => $img->width(),
                'new_height' => $img->height(),
            ]);
        }

        // Конвертируем в WebP и сжимаем
        $webpData = $img->encode('webp', self::WEBP_QUALITY)->getEncoded();

        // Сохраняем во временный файл
        $tempFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.webp';
        $tempPath = sys_get_temp_dir() . '/' . uniqid('img_compress_') . '.webp';
        file_put_contents($tempPath, $webpData);

        $newSize = filesize($tempPath);

        Log::info('[CompressImageTask] Сжатие завершено', [
            'original_size' => $this->formatFileSize($file->getSize()),
            'compressed_size' => $this->formatFileSize($newSize),
            'compression_ratio' => round((1 - $newSize / $file->getSize()) * 100, 2) . '%',
            'temp_path' => $tempPath,
        ]);

        // Создаём новый UploadedFile из сжатого
        $compressedFile = new UploadedFile(
            $tempPath,
            $tempFileName,
            'image/webp',
            $newSize,
            true
        );

        return [
            'file' => $compressedFile,
            'size' => $newSize,
            'original_size' => $file->getSize(),
            'compressed' => true,
            'compression_ratio' => round((1 - $newSize / $file->getSize()) * 100, 2),
        ];
    }

    /**
     * Проверяет, является ли файл изображением
     */
    public function isImage(UploadedFile $file): bool
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $imageExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp', 'tiff', 'svg'];

        return in_array($extension, $imageExtensions);
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
