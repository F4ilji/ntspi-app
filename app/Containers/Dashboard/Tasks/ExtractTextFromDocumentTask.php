<?php

namespace App\Containers\Dashboard\Tasks;

use App\Containers\Dashboard\Tasks\ParseDocxTask;
use App\Containers\Dashboard\Tasks\ConvertDocToDocxTask;
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

        Log::info('[ExtractTextFromDocumentTask] Начало извлечения текста', [
            'file' => $file->getClientOriginalName(),
            'extension' => $extension,
            'size' => $file->getSize(),
        ]);

        // Если DOCX — парсим напрямую
        if ($extension === 'docx') {
            Log::info('[ExtractTextFromDocumentTask] Обработка DOCX файла');
            return $this->extractFromDocx($file);
        }

        // Если DOC — конвертируем в DOCX, затем парсим
        if ($extension === 'doc') {
            Log::info('[ExtractTextFromDocumentTask] Обработка DOC файла (требуется конвертация)');
            return $this->extractFromDoc($file);
        }

        Log::warning('[ExtractTextFromDocumentTask] Неизвестный формат файла', [
            'extension' => $extension,
        ]);

        return null;
    }

    /**
     * Извлекает текст из DOCX файла
     */
    private function extractFromDocx(UploadedFile $file): ?string
    {
        Log::info('[ExtractTextFromDocumentTask:extractFromDocx] Начало');

        // Сохраняем во временную директорию
        $tempPath = $file->storeAs('temp', 'temp_doc_' . time() . '.docx', 'local');
        $absoluteTempPath = Storage::disk('local')->path($tempPath);

        Log::info('[ExtractTextFromDocumentTask:extractFromDocx] Файл сохранен', [
            'path' => $tempPath,
            'absolute_path' => $absoluteTempPath,
        ]);

        // Парсим файл
        $extractedText = $this->parseDocxTask->run($absoluteTempPath);

        Log::info('[ExtractTextFromDocumentTask:extractFromDocx] Результат парсинга', [
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
        Log::info('[ExtractTextFromDocumentTask:extractFromDoc] Начало конвертации');

        // Конвертируем DOC → DOCX
        $docxPath = $this->convertDocToDocxTask->run($file);

        if (!$docxPath) {
            Log::error('[ExtractTextFromDocumentTask:extractFromDoc] Не удалось конвертировать DOC файл', [
                'file' => $file->getClientOriginalName(),
            ]);
            return null;
        }

        Log::info('[ExtractTextFromDocumentTask:extractFromDoc] Конвертация успешна', [
            'docx_path' => $docxPath,
        ]);

        // Получаем абсолютный путь
        $absoluteDocxPath = Storage::disk('local')->path($docxPath);

        // Парсим конвертированный файл
        $extractedText = $this->parseDocxTask->run($absoluteDocxPath);

        Log::info('[ExtractTextFromDocumentTask:extractFromDoc] Результат парсинга', [
            'text_length' => strlen($extractedText ?? ''),
        ]);

        // Удаляем конвертированный файл
        Storage::disk('local')->delete($docxPath);

        return $extractedText;
    }
}
