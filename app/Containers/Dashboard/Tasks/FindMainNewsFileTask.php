<?php

namespace App\Containers\Dashboard\Tasks;

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
        Log::info('[FindMainNewsFileTask] Начало поиска основного файла', [
            'files_count' => $files->count(),
            'files' => $files->map(function($f) {
                return $f->getClientOriginalName() . ' (' . $f->getClientOriginalExtension() . ')';
            })->toArray(),
        ]);

        // Фильтруем DOC и DOCX файлы (поддерживаем оба формата)
        $docFiles = $files->filter(function($file) {
            $ext = strtolower($file->getClientOriginalExtension());
            return in_array($ext, ['doc', 'docx']);
        });

        Log::info('[FindMainNewsFileTask] Найдено DOC/DOCX файлов', [
            'count' => $docFiles->count(),
            'files' => $docFiles->map(fn($f) => $f->getClientOriginalName())->toArray(),
        ]);

        if ($docFiles->isEmpty()) {
            Log::warning('[FindMainNewsFileTask] Не найдено DOC/DOCX файлов');
            return null;
        }

        // Если только один файл — используем его
        if ($docFiles->count() === 1) {
            Log::info('[FindMainNewsFileTask] Найден один файл, используем его', [
                'file' => $docFiles->first()->getClientOriginalName(),
            ]);
            return $docFiles->first();
        }

        // Ищем файл с ключевыми словами в названии
        foreach ($docFiles as $file) {
            $filename = mb_strtolower($file->getClientOriginalName());
            foreach (self::NEWS_KEYWORDS as $keyword) {
                if (str_contains($filename, $keyword)) {
                    Log::info('[FindMainNewsFileTask] Найден файл по ключевому слову', [
                        'file' => $file->getClientOriginalName(),
                        'keyword' => $keyword,
                    ]);
                    return $file;
                }
            }
        }

        Log::info('[FindMainNewsFileTask] Не найдено файлов по ключевым словам, используем LLM');

        // Если не нашли по ключевым словам — используем LLM
        return $this->findViaLLM($docFiles);
    }

    /**
     * Определяет основной файл через LLM
     */
    private function findViaLLM(Collection $docFiles): ?UploadedFile
    {
        Log::info('[FindMainNewsFileTask:findViaLLM] Начало LLM определения', [
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
            Log::info('[FindMainNewsFileTask:findViaLLM] Извлечение фрагмента', [
                'file' => $file->getClientOriginalName(),
            ]);
            
            $fragment = app(ExtractTextFragmentTask::class)->run($file, 300);
            $fragments[] = [
                'filename' => $file->getClientOriginalName(),
                'text' => $fragment,
            ];
        }

        // Отправляем запрос к LLM
        Log::info('[FindMainNewsFileTask:findViaLLM] Отправка запроса к LLM');
        $llmResponse = app(CallAiServiceForFileSelectionTask::class)->run($fragments);

        if ($llmResponse && isset($llmResponse['main_file'])) {
            Log::info('[FindMainNewsFileTask:findViaLLM] LLM вернула результат', [
                'main_file' => $llmResponse['main_file'],
            ]);
            
            $mainFilename = $llmResponse['main_file'];
            foreach ($docFiles as $file) {
                if ($file->getClientOriginalName() === $mainFilename) {
                    return $file;
                }
            }
        }

        Log::warning('[FindMainNewsFileTask:findViaLLM] LLM не помогла, используем первый файл');
        
        // Fallback — первый файл
        return $docFiles->first();
    }
}
