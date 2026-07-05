<?php

namespace App\Containers\Dashboard\Tasks\AI;

use App\Containers\Dashboard\Tasks\AI\ParseDocxTask;
use App\Containers\Dashboard\Tasks\AI\ConvertDocToDocxTask;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ExtractTextFromDocumentTask
{
    public function __construct(
        private readonly ParseDocxTask $parseDocxTask,
        private readonly ConvertDocToDocxTask $convertDocToDocxTask,
    ) {}

    /**
     * Извлекает текст из DOCX или DOC файла
     * Автоматически определяет формат и конвертирует при необходимости
     */
    public function run(UploadedFile $file): ?string
    {
        $extension = strtolower($file->getClientOriginalExtension());

        Log::channel('ai')->info('Начало извлечения текста', [
            'file' => $file->getClientOriginalName(),
            'extension' => $extension,
            'size' => $file->getSize(),
        ]);

        // Если DOCX — парсим напрямую
        if ($extension === 'docx') {
            Log::channel('ai')->info('Обработка DOCX файла');
            return $this->extractFromDocx($file);
        }

        // Если DOC — конвертируем в DOCX, затем парсим
        if ($extension === 'doc') {
            Log::channel('ai')->info('Обработка DOC файла (требуется конвертация)');
            return $this->extractFromDoc($file);
        }

        Log::channel('ai')->warning('Неизвестный формат файла', [
            'extension' => $extension,
        ]);

        return null;
    }

    /**
     * Извлекает текст из DOCX файла
     */
    private function extractFromDocx(UploadedFile $file): ?string
    {
        Log::channel('ai')->info('Начало');

        // Сохраняем во временную директорию
        $tempPath = $file->storeAs('temp', 'temp_doc_' . time() . '.docx', 'local');
        $absoluteTempPath = Storage::disk('local')->path($tempPath);

        Log::channel('ai')->info('Файл сохранен', [
            'path' => $tempPath,
            'absolute_path' => $absoluteTempPath,
        ]);

        // Парсим файл
        $extractedText = $this->parseDocxTask->run($absoluteTempPath);

        Log::channel('ai')->info('Результат парсинга', [
            'text_length' => strlen($extractedText ?? ''),
        ]);

        // Удаляем временный файл
        Storage::disk('local')->delete($tempPath);

        return $extractedText;
    }

    /**
     * Извлекает текст из DOC файла (через конвертацию)
     */
    private function extractFromDoc(UploadedFile $file): ?string
    {
        Log::channel('ai')->info('Начало конвертации');

        // Конвертируем DOC → DOCX
        $docxPath = $this->convertDocToDocxTask->run($file);

        if (!$docxPath) {
            Log::channel('ai')->error('Не удалось конвертировать DOC файл', [
                'file' => $file->getClientOriginalName(),
            ]);
            return null;
        }

        Log::channel('ai')->info('Конвертация успешна', [
            'docx_path' => $docxPath,
        ]);

        // Получаем абсолютный путь
        $absoluteDocxPath = Storage::disk('local')->path($docxPath);

        // Парсим конвертированный файл
        $extractedText = $this->parseDocxTask->run($absoluteDocxPath);

        Log::channel('ai')->info('Результат парсинга', [
            'text_length' => strlen($extractedText ?? ''),
        ]);

        // Удаляем конвертированный файл
        Storage::disk('local')->delete($docxPath);

        return $extractedText;
    }
}
