<?php

namespace App\Containers\Dashboard\Tasks\AI;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ExtractTextFragmentTask
{
    /**
     * Извлекает фрагмент текста (первые N символов) из DOCX или DOC файла
     */
    public function run(UploadedFile $file, int $maxLength = 300): string
    {
        Log::info('[ExtractTextFragmentTask] Начало извлечения фрагмента', [
            'file' => $file->getClientOriginalName(),
            'extension' => $file->getClientOriginalExtension(),
        ]);

        $extension = strtolower($file->getClientOriginalExtension());

        // Для DOCX — парсим напрямую
        if ($extension === 'docx') {
            return $this->extractFromDocx($file, $maxLength);
        }

        // Для DOC — конвертируем и парсим
        if ($extension === 'doc') {
            return $this->extractFromDoc($file, $maxLength);
        }

        Log::warning('[ExtractTextFragmentTask] Неизвестный формат', [
            'extension' => $extension,
        ]);

        return '(неподдерживаемый формат)';
    }

    /**
     * Извлекает фрагмент из DOCX
     */
    private function extractFromDocx(UploadedFile $file, int $maxLength): string
    {
        // Сохраняем во временную директорию
        $tempPath = $file->storeAs('temp', 'temp_fragment_' . time() . '.docx', 'local');
        $absoluteTempPath = Storage::disk('local')->path($tempPath);

        // Парсим файл через ParseDocxTask
        $fullText = app(ParseDocxTask::class)->run($absoluteTempPath);

        // Удаляем временный файл
        Storage::disk('local')->delete($tempPath);

        return $this->truncateText($fullText, $maxLength);
    }

    /**
     * Извлекает фрагмент из DOC (через конвертацию)
     */
    private function extractFromDoc(UploadedFile $file, int $maxLength): string
    {
        // Используем ExtractTextFromDocumentTask для конвертации и извлечения
        $extractor = app(ExtractTextFromDocumentTask::class);
        $fullText = $extractor->run($file);

        return $this->truncateText($fullText, $maxLength);
    }

    /**
     * Обрезает текст до нужной длины
     */
    private function truncateText(?string $text, int $maxLength): string
    {
        if (empty($text)) {
            return '(пустой файл)';
        }

        if (strlen($text) > $maxLength) {
            return substr($text, 0, $maxLength) . '...';
        }

        return $text;
    }
}
