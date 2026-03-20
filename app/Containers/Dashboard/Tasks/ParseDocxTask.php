<?php

namespace App\Containers\Dashboard\Tasks;

use ZipArchive;
use Illuminate\Support\Facades\Log;

class ParseDocxTask
{
    /**
     * Извлекает текст из .docx файла с подробным логированием ошибок
     */
    public function run(string $filePath): ?string
    {
        // 1. Проверяем, существует ли физически файл по этому пути
        if (!file_exists($filePath)) {
            Log::error("[ParseDocxTask] Файл не найден по пути: {$filePath}");
            return "ОШИБКА ДЕБАГА: Файл не найден на сервере.";
        }

        $zip = new ZipArchive();

        // 2. Пытаемся открыть архив
        $res = $zip->open($filePath);

        // Если не true, значит произошла ошибка
        if ($res !== true) {
            $errorMsg = $this->getZipError($res);
            Log::error("[ParseDocxTask] Ошибка ZipArchive: {$errorMsg} | Путь: {$filePath}");

            // Возвращаем текст ошибки прямо на фронт, чтобы сразу увидеть причину!
            return "ОШИБКА ДЕБАГА ZipArchive: {$errorMsg}";
        }

        // 3. Пытаемся достать нужный XML файл
        $xmlString = $zip->getFromName('word/document.xml');

        // Обязательно закрываем архив, чтобы не было утечек памяти
        $zip->close();

        if ($xmlString === false) {
            Log::error("[ParseDocxTask] Файл word/document.xml не найден внутри архива. Возможно, это не настоящий DOCX.");
            return "ОШИБКА ДЕБАГА: Внутри архива нет word/document.xml";
        }

        // 4. Парсим XML
        try {
            $text = str_replace('</w:p>', " \n", $xmlString);
            $text = strip_tags($text);
            return trim($text);
        } catch (\Exception $e) {
            Log::error("[ParseDocxTask] Ошибка очистки тегов: " . $e->getMessage());
            return "ОШИБКА ДЕБАГА при очистке текста.";
        }
    }

    /**
     * Превращает числовой код ошибки ZipArchive в текст
     */
    private function getZipError(int $code): string
    {
        $errors = [
            ZipArchive::ER_EXISTS => 'Файл уже существует.',
            ZipArchive::ER_INCONS => 'Несовместимый ZIP-архив.',
            ZipArchive::ER_INVAL => 'Недопустимый аргумент.',
            ZipArchive::ER_MEMORY => 'Ошибка выделения памяти (Malloc).',
            ZipArchive::ER_NOENT => 'Нет такого файла.',
            ZipArchive::ER_NOZIP => 'Это НЕ ZIP-архив (Скорее всего файл переименован или битый).',
            ZipArchive::ER_OPEN => 'Невозможно открыть файл (Проблема с правами).',
            ZipArchive::ER_READ => 'Ошибка чтения.',
            ZipArchive::ER_SEEK => 'Ошибка поиска (Seek error).',
        ];

        return $errors[$code] ?? "Неизвестная ошибка с кодом: {$code}";
    }
}