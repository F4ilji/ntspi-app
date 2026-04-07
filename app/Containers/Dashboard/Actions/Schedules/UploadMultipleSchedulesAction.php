<?php

namespace App\Containers\Dashboard\Actions\Schedules;

use App\Containers\Schedule\Models\EducationalGroup;
use App\Containers\Schedule\Models\Schedule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class UploadMultipleSchedulesAction
{
    private array $processedFiles = [];
    private array $failedFiles = [];

    /**
     * Обрабатывает множественную загрузку файлов расписаний
     *
     * @param array $files Массив загруженных файлов
     * @return array Результаты обработки
     */
    public function run(array $files): array
    {
        $this->processedFiles = [];
        $this->failedFiles = [];

        foreach ($files as $file) {
            $this->processFile($file);
        }

        return [
            'processed' => $this->processedFiles,
            'failed' => $this->failedFiles,
            'processed_count' => count($this->processedFiles),
            'failed_count' => count($this->failedFiles),
        ];
    }

    /**
     * Обрабатывает одиночный файл
     */
    private function processFile($file): void
    {
        try {
            $originalFileNameWithExtension = $file->getClientOriginalName();
            $originalFileName = pathinfo($originalFileNameWithExtension, PATHINFO_FILENAME);

            // Проверяем, был ли файл уже обработан
            if (in_array($originalFileNameWithExtension, array_column($this->processedFiles, 'filename'))) {
                return;
            }

            $educationalGroup = $this->findEducationalGroupByFileName($originalFileName);

            if (!$educationalGroup) {
                // Извлекаем очищенное название для более понятного сообщения об ошибке
                $fileNameWithoutExt = pathinfo($originalFileName, PATHINFO_FILENAME);
                $cleanedName = preg_replace('/\s+с\s+\d{2}\.\d{2}(\.\d{4})?\s+по\s+\d{2}\.\d{2}(\.\d{4})?/ui', '', $fileNameWithoutExt);
                $cleanedName = preg_replace('/\s*(Экзамены|расписание|сессия|контрольная|\d{4}).*$/ui', '', $cleanedName);
                $cleanedName = trim(preg_replace('/\s+/', ' ', $cleanedName));
                
                $this->failedFiles[] = [
                    'filename' => $originalFileNameWithExtension,
                    'error' => "Группа '{$cleanedName}' не найдена в базе. Проверьте правильность названия группы.",
                ];
                return;
            }

            // Проверяем наличие расписания для данной группы
            $existingSchedule = Schedule::where('educational_group_id', $educationalGroup->id)
                ->whereJsonContains('file->0->title', $originalFileName)
                ->first();

            if ($existingSchedule) {
                $this->failedFiles[] = [
                    'filename' => $originalFileNameWithExtension,
                    'error' => 'Расписание уже существует',
                ];
                return;
            }

            // Сохраняем файл
            $uniqueFileName = Str::slug($originalFileName) . '-' . Carbon::now()->timestamp . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('schedules', $uniqueFileName, 'public');

            // Создаем запись
            DB::transaction(function () use ($educationalGroup, $originalFileName, $path) {
                Schedule::create([
                    'educational_group_id' => $educationalGroup->id,
                    'file' => [
                        [
                            'title' => $originalFileName,
                            'path' => $path,
                        ],
                    ],
                ]);
            });

            $this->processedFiles[] = [
                'filename' => $originalFileNameWithExtension,
                'group' => $educationalGroup->title,
            ];

        } catch (\Throwable $e) {
            $this->failedFiles[] = [
                'filename' => $file->getClientOriginalName(),
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Находит образовательную группу по названию файла
     *
     * Алгоритм:
     * 1. Очищаем название файла от лишней информации (даты, слова "Экзамены", "расписание" и т.д.)
     * 2. Извлекаем код группы по паттернам
     * 3. Ищем точное совпадение в базе
     */
    private function findEducationalGroupByFileName(string $fileName): ?EducationalGroup
    {
        $fileNameWithoutExt = pathinfo($fileName, PATHINFO_FILENAME);

        // Нормализуем имя файла - убираем лишние пробелы
        $fileNameWithoutExt = preg_replace('/\s+/', ' ', trim($fileNameWithoutExt));

        // ========== ШАГ 1: Очищаем название файла от лишней информации ==========
        
        // Удаляем даты в форматах: "с 06.04 по 09.04.2026", "с 10.01.2026 по 15.01.2026"
        $cleanedName = preg_replace('/\s+с\s+\d{2}\.\d{2}(\.\d{4})?\s+по\s+\d{2}\.\d{2}(\.\d{4})?/ui', '', $fileNameWithoutExt);
        
        // Удаляем слова "Экзамены", "расписание", "сессия" и годы
        $cleanedName = preg_replace('/\s*(Экзамены|расписание|сессия|контрольная|\d{4}).*$/ui', '', $cleanedName);
        
        // Финальная очистка - убираем лишние пробелы
        $cleanedName = trim(preg_replace('/\s+/', ' ', $cleanedName));

        // ========== ШАГ 2: Извлечение кода группы по паттернам ==========

        // Паттерн 1: "Нт-XXXо YYY" или "Нт-XXXоYYY" (очная форма, например: Нт-101о ИРЭ, Нт-211Со Экл)
        if (preg_match('/^(Нт-\d{2,3}[а-яА-ЯёЁ]?\s*[а-яА-ЯёЁ]{2,5})/u', $cleanedName, $matches)) {
            $groupCode = trim($matches[1]);
            // Нормализуем: убираем лишние пробелы между номером и буквами
            $groupCode = preg_replace('/(\d)(\s+)([а-яА-ЯёЁ])/u', '$1$3', $groupCode);

            // Ищем ТОЧНОЕ совпадение
            $group = EducationalGroup::where('title', $groupCode)->first();
            if ($group) {
                return $group;
            }
        }

        // Паттерн 2: "Нт-XXX YYY" (заочная форма, например: Нт-404 ИП, Нт-501 СР)
        if (preg_match('/^(Нт-\d{3}\s+[А-Я]{2,4})(?![а-яА-Я])/u', $cleanedName, $matches)) {
            $groupCode = trim($matches[1]);
            $group = EducationalGroup::where('title', $groupCode)->first();
            if ($group) {
                return $group;
            }
        }

        // Паттерн 3: "нXXX YY-о3" (например: н101 ГД-о3, н303 ФК-По3)
        if (preg_match('/^(н\d{3}\s+[А-Яа-я\-]+-о[34])/u', $cleanedName, $matches)) {
            $groupCode = trim($matches[1]);
            $group = EducationalGroup::where('title', $groupCode)->first();
            if ($group) {
                return $group;
            }
        }

        // Паттерн 4: "Нт-XXX мYYY" (магистратура, например: Нт-102 мТМОД)
        if (preg_match('/^(Нт-\d{3}\s+м[А-Я]{2,5})/u', $cleanedName, $matches)) {
            $groupCode = trim($matches[1]);
            $group = EducationalGroup::where('title', $groupCode)->first();
            if ($group) {
                return $group;
            }
        }

        // Паттерн 5: "БФКф-XXXX" (например: БФКф-2531)
        if (preg_match('/^(БФКф-\d{4})/u', $cleanedName, $matches)) {
            $groupCode = trim($matches[1]);
            $group = EducationalGroup::where('title', $groupCode)->first();
            if ($group) {
                return $group;
            }
        }

        // ========== ШАГ 3: Точное совпадение по очищенному имени файла ==========
        $exactMatch = EducationalGroup::where('title', $cleanedName)->first();
        if ($exactMatch) {
            return $exactMatch;
        }

        return null;
    }
}
