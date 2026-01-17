<?php

namespace App\Filament\Resources\ScheduleResource\Pages\ScheduleResource\Pages;

use App\Filament\Resources\ScheduleResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;

use App\Containers\Schedule\Models\EducationalGroup;
use App\Containers\Schedule\Models\Schedule;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile; // Убедитесь, что этот use-оператор присутствует

class UploadSchedules extends Page
{
    protected static string $resource = ScheduleResource::class;

    protected static ?string $title = 'Быстрая загрузка расписания';

    protected static string $view = 'filament.resources.schedule-resource.pages.schedule-resource.pages.upload-schedules';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-on-square';

    protected static ?string $navigationLabel = 'Быстрая загрузка';

//    protected static ?int $navigationSort = 1;

    // `public ?array $data = [];` - это не нужно, если вы используете $schedules_files напрямую для FileUpload

    /**
     * Публичное свойство для FileUpload.
     * Livewire/Filament ожидает его наличия для корректной работы компонента формы.
     * Будет содержать массив TemporaryUploadedFile или null.
     */
    public $schedules_files = []; // Инициализируем как пустой массив

    public array $processedFiles = [];
    public array $failedFiles = [];

    public function mount(): void
    {
        // Не нужно вызывать $this->form->fill(); если нет начальных данных для формы.
        // FileUpload управляет своим состоянием сам.
        $this->processedFiles = [];
        $this->failedFiles = [];
        $this->schedules_files = []; // Инициализируем как пустой массив при монтировании
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Загрузка расписаний')
                    ->description('Загрузите несколько файлов расписаний одновременно. Система автоматически определит группы и создаст записи.')
                    ->schema([
                        FileUpload::make('schedules_files')
                            ->label('Файлы расписаний')
                            ->multiple()
                            ->required()
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(10000) // 10MB
                            ->disk('public')
                            ->directory('schedules')
                            ->helperText('Загрузите PDF файлы. Название файла должно содержать название группы (например, "Нт-102 мТМОД Экзамены.pdf").')
                        // Удаляем afterStateUpdated, так как обработка будет в save()
                        // ->afterStateUpdated(function ($state, callable $set) { /* ... */ }),
                    ]),
            ])
            // Важно: если FileUpload напрямую привязан к публичному свойству $schedules_files,
            // то statePath('data') не нужен для этого конкретного поля.
            // Если у вас есть другие поля, которые вы хотите привязать к $data, оставьте statePath('data').
            // Но для FileUpload, привязанного к $schedules_files, он будет напрямую изменять это свойство.
            // Если вы хотите, чтобы все поля были в $data, то тогда FileUpload должен быть 'data.schedules_files'
            // в схеме формы, и вы будете обращаться к $this->data['schedules_files'].
            // Для простоты, оставим его напрямую привязанным к $schedules_files.
            // Если у вас нет других полей формы, можете вообще удалить statePath.
            // В данном случае, поскольку у нас только один FileUpload, его лучше не привязывать к $data.
            // $this->data = $form->getState(); // Это не нужно при такой привязке
            ;
    }

    /**
     * Логика обработки одиночного загруженного файла.
     * Теперь вызывается только из метода save().
     * @param TemporaryUploadedFile $file
     * @return bool Успех обработки
     */
    protected function processUploadedFile(TemporaryUploadedFile $file): bool
    {
        try {
            $originalFileNameWithExtension = $file->getClientOriginalName();
            $originalFileName = pathinfo($originalFileNameWithExtension, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();

            // Проверяем, был ли файл уже успешно обработан в этой сессии
            // Это дополнительная защита от дублирования, хотя основной баг исправлен переносом логики.
            if (in_array($originalFileNameWithExtension, $this->processedFiles)) {
                return true; // Уже обработан, ничего не делаем
            }

            $educationalGroup = $this->findEducationalGroupByFileName($originalFileName);

            if (!$educationalGroup) {
                $this->failedFiles[] = $originalFileNameWithExtension;
                Notification::make()
                    ->title('Группа не найдена')
                    ->body("Не удалось найти группу для файла: `{$originalFileNameWithExtension}`. Проверьте название файла.")
                    ->warning()
                    ->send();
                return false;
            }

            // Проверяем наличие расписания для данной группы с похожим названием файла
            // Это предотвратит создание дубликатов в базе данных, если пользователь загрузит тот же файл.
            $existingSchedule = Schedule::where('educational_group_id', $educationalGroup->id)
                ->whereJsonContains('file->0->title', $originalFileName)
                ->first();

            if ($existingSchedule) {
                $this->failedFiles[] = $originalFileNameWithExtension . " (уже существует)";
                Notification::make()
                    ->title('Расписание уже существует')
                    ->body("Расписание для группы `{$educationalGroup->title}` с файлом `{$originalFileNameWithExtension}` уже существует.")
                    ->info()
                    ->send();
                return false;
            }


            // Перемещаем файл в публичное хранилище
            $sluggedFileName = Str::slug($originalFileName);
            // Добавляем timestamp для уникальности, но убедимся, что он достаточно уникален
            $uniqueFileName = $sluggedFileName . '-' . md5(uniqid(rand(), true)) . '.' . $extension; // Более уникальный ID
            $path = $file->storeAs('schedules', $uniqueFileName, 'public');

            // Создаем запись расписания
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

            $this->processedFiles[] = $originalFileNameWithExtension;
            return true;

        } catch (\Throwable $e) {
            $this->failedFiles[] = $originalFileNameWithExtension . " (Ошибка: " . $e->getMessage() . ")";
            Notification::make()
                ->title('Ошибка при обработке файла')
                ->body("Не удалось обработать файл `{$originalFileNameWithExtension}`: " . $e->getMessage())
                ->danger()
                ->send();
            report($e);
            return false;
        }
    }

    public function save(): void
    {
        // Сбрасываем списки обработанных/необработанных файлов перед новой попыткой сохранения.
        $this->processedFiles = [];
        $this->failedFiles = [];

        // Проверяем, есть ли файлы для обработки
        if (empty($this->schedules_files)) {
            Notification::make()
                ->title('Нет файлов для обработки')
                ->warning()
                ->send();
            return;
        }

        // Проходим по всем загруженным файлам и обрабатываем их
        foreach ($this->schedules_files as $file) {
            if ($file instanceof TemporaryUploadedFile) {
                $this->processUploadedFile($file);
            }
        }

        // Отправляем итоговое уведомление
        $hasProcessed = !empty($this->processedFiles);
        $hasFailed = !empty($this->failedFiles);

        if ($hasProcessed || $hasFailed) {
            $notification = Notification::make();

            if ($hasProcessed && $hasFailed) {
                $notification
                    ->title('Частично обработано')
                    ->body('Успешно: ' . count($this->processedFiles) . '. Ошибки: ' . count($this->failedFiles) . '. Подробности в логах.')
                    ->warning();
            } elseif ($hasProcessed) {
                $notification
                    ->title('Все файлы успешно загружены')
                    ->body('Успешно обработано: ' . count($this->processedFiles) . ' файл(ов)')
                    ->success();
            } else { // $hasFailed only
                $notification
                    ->title('Ошибка при обработке файлов')
                    ->body('Ошибки при обработке: ' . count($this->failedFiles) . ' файл(ов)')
                    ->danger();
            }

            $notification->send();
        }

        // Очищаем форму и связанные свойства после обработки
        // Важно очистить $schedules_files, чтобы избежать повторной обработки при следующей отправке
        $this->schedules_files = [];
        // Если у вас есть statePath, можно вызвать $this->form->fill();
        // В данном случае, так как $schedules_files напрямую привязан, достаточно обнулить его.
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Загрузить и показать результаты') // Изменил текст для ясности
                ->submit('save')
                ->color('primary')
                ->icon('heroicon-o-cloud-arrow-up')
            // Кнопка всегда видна, чтобы пользователь мог инициировать обработку
            // даже если он просто выбрал файлы, но они еще не были обработаны.
            // Также полезно для вывода результатов после загрузки.
            // ->hidden(fn () => empty($this->processedFiles) && empty($this->failedFiles))
        ];
    }


    public static function getNavigationLabel(): string
    {
        return __('Расписание и группы');
    }

    /**
     * Находит образовательную группу по названию файла, используя различные стратегии сопоставления
     *
     * @param string $fileName
     * @return EducationalGroup|null
     */
    protected function findEducationalGroupByFileName(string $fileName): ?EducationalGroup
    {
        // Удаляем расширение файла, если оно есть
        $fileNameWithoutExt = pathinfo($fileName, PATHINFO_FILENAME);

        // Попытка 1: поиск точного совпадения
        $exactMatch = EducationalGroup::where('title', $fileNameWithoutExt)->first();
        if ($exactMatch) {
            return $exactMatch;
        }

        // Попытка 2: используем регулярные выражения для поиска групп вида БФКф-2531, Нт-102 и т.д.
        // Шаблон: любая последовательность букв/цифр с дефисом и цифрой
        if (preg_match('/([А-Яа-яЁёA-Za-z]+[-_\s]?[\dА-Яа-яЁёA-Za-z]*\d)/u', $fileName, $matches)) {
            $potentialGroupCode = trim($matches[1]);

            // Убираем лишние символы вроде пробелов и дефисов в конце
            $potentialGroupCode = preg_replace('/[-_\s]+$/', '', $potentialGroupCode);

            // Попытка найти группу по коду
            $group = EducationalGroup::where('title', 'like', '%' . $potentialGroupCode . '%')->first();
            if ($group) {
                return $group;
            }
        }

        // Попытка 3: разбиение на слова и поиск по первым двум словам (старая логика)
        $words = preg_split('/\s+/', $fileName, 3); // разбиваем на 3 части максимум
        if (count($words) >= 2) {
            $groupTitleFromWords = $words[0] . ' ' . $words[1];
            $group = EducationalGroup::where('title', 'like', $groupTitleFromWords . '%')->first();
            if ($group) {
                return $group;
            }
        }

        // Попытка 4: если осталось только одно слово
        if (isset($words[0])) {
            $group = EducationalGroup::where('title', 'like', $words[0] . '%')->first();
            if ($group) {
                return $group;
            }
        }

        // Если ничего не нашли, возвращаем null
        return null;
    }
}
