<?php

namespace App\Containers\Dashboard\Tasks\AI;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class FindMainNewsFileTask
{
    /**
     * Ключевые слова для определения файла с новостью
     */
    private const NEWS_KEYWORDS = [
        'новость', 'новости', 'заметка', 'заметки', 'статья', 'статьи',
        'материал', 'материалы', 'текст', 'контент', 'публикация',
        'news', 'article', 'post', 'material', 'text',
        'релиз', 'пресс', 'отчет', 'отчёт', 'доклад'
    ];

    /**
     * Находит основной файл с текстом новости среди всех загруженных файлов
     *
     * @param Collection<UploadedFile> $files Все загруженные файлы
     * @return UploadedFile|null Основной файл или null если не найден
     */
    public function run(Collection $files): ?UploadedFile
    {
        Log::channel('ai')->info('Начало поиска основного файла', [
            'files_count' => $files->count(),
            'files' => $files->map(function($f) {
                return $f->getClientOriginalName() . ' (' . $f->getClientOriginalExtension() . ')';
            })->toArray(),
        ]);

        // Фильтруем DOC и DOCX файлы (поддерживаем оба формата)
        $docFiles = $files->filter(function($file) {
            $ext = strtolower($file->getClientOriginalExtension());
            $mimeType = $file->getMimeType();
            
            // Проверяем по расширению
            if (in_array($ext, ['doc', 'docx'])) {
                return true;
            }
            
            // Проверяем по MIME-типу (для случаев, когда расширение не определено)
            $documentMimes = [
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ];
            if (in_array($mimeType, $documentMimes, true)) {
                return true;
            }
            
            // Проверяем по содержимому имени файла (для MIME-кодированных имён)
            $filename = $file->getClientOriginalName();
            if (str_contains(strtolower($filename), '.docx') || str_contains(strtolower($filename), '.doc')) {
                return true;
            }
            
            return false;
        });

        Log::channel('ai')->info('Найдено DOC/DOCX файлов', [
            'count' => $docFiles->count(),
            'files' => $docFiles->map(fn($f) => $f->getClientOriginalName())->toArray(),
        ]);

        if ($docFiles->isEmpty()) {
            Log::channel('ai')->warning('Не найдено DOC/DOCX файлов');
            return null;
        }

        // Если только один файл — используем его
        if ($docFiles->count() === 1) {
            Log::channel('ai')->info('Найден один файл, используем его', [
                'file' => $docFiles->first()->getClientOriginalName(),
            ]);
            return $docFiles->first();
        }

        // Ищем файл с ключевыми словами в названии
        foreach ($docFiles as $file) {
            $filename = mb_strtolower($file->getClientOriginalName());
            foreach (self::NEWS_KEYWORDS as $keyword) {
                if (str_contains($filename, $keyword)) {
                    Log::channel('ai')->info('Найден файл по ключевому слову', [
                        'file' => $file->getClientOriginalName(),
                        'keyword' => $keyword,
                    ]);
                    return $file;
                }
            }
        }

        Log::channel('ai')->info('Не найдено файлов по ключевым словам, используем LLM');

        // Если не нашли по ключевым словам — используем LLM
        return $this->findViaLLM($docFiles);
    }

    /**
     * Определяет основной файл через LLM
     */
    private function findViaLLM(Collection $docFiles): ?UploadedFile
    {
        Log::channel('ai')->info('Начало LLM определения', [
            'files_count' => $docFiles->count(),
        ]);

        if ($docFiles->isEmpty()) {
            return null;
        }

        if ($docFiles->count() === 1) {
            return $docFiles->first();
        }

        // Собираем фрагменты текста из каждого файла
        $fragments = [];
        foreach ($docFiles as $file) {
            Log::channel('ai')->info('Извлечение фрагмента', [
                'file' => $file->getClientOriginalName(),
            ]);
            
            $fragment = app(ExtractTextFragmentTask::class)->run($file, 300);
            $fragments[] = [
                'filename' => $file->getClientOriginalName(),
                'text' => $fragment,
            ];
        }

        // Отправляем запрос к LLM
        Log::channel('ai')->info('Отправка запроса к LLM');
        $llmResponse = app(CallAiServiceForFileSelectionTask::class)->run($fragments);

        if ($llmResponse && isset($llmResponse['main_file'])) {
            Log::channel('ai')->info('LLM вернула результат', [
                'main_file' => $llmResponse['main_file'],
            ]);
            
            $mainFilename = $llmResponse['main_file'];
            foreach ($docFiles as $file) {
                if ($file->getClientOriginalName() === $mainFilename) {
                    return $file;
                }
            }
        }

        Log::channel('ai')->warning('LLM не помогла, используем первый файл');
        
        // Fallback — первый файл
        return $docFiles->first();
    }
}
