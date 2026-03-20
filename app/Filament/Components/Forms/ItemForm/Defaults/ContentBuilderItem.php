<?php

namespace App\Filament\Components\Forms\ItemForm\Defaults;

use App\Containers\Article\Models\Category;
use App\Containers\Widget\Models\CustomForm;
use App\Containers\Widget\Models\PageReferenceList;
use App\Ship\Helpers\ByteConverter;
use App\Containers\AppStructure\Models\Page;
use App\Containers\Article\Models\Post;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class ContentBuilderItem
{
    public static function getItem(string $name)
    {
        return Builder::make($name)
            ->label('Конструктор содержимого')
            ->blocks([
                // Заголовок
                Builder\Block::make('heading')
                    ->icon('heroicon-o-title')
                    ->label('Заголовок')
                    ->schema([
                        TextInput::make('id')
                            ->hidden()
                            ->integer()
                            ->default(rand(2335235, 324634264263426)),

                        TextInput::make('content')
                            ->label('Текст заголовка')
                            ->placeholder('Введите заголовок H2-H4')
                            ->hint('Рекомендуется 50-80 символов')
                            ->helperText('Используйте для семантической структуры')
                            ->minLength(10)
                            ->maxLength(120)
                            ->required()
                            ->live(onBlur: true),
                    ]),

                // Текстовый блок
                Builder\Block::make('paragraph')
                    ->icon('heroicon-o-document-text')
                    ->label('Текстовый блок')
                    ->schema([
                        TinyEditor::make('content')
                            ->label('Содержимое')
                            ->placeholder('Введите текст...')
                            ->hint('Поддерживается форматирование')
                            ->helperText('Для заголовков используйте стили H3-H4')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                // Файлы
                Builder\Block::make('files')
                    ->icon('heroicon-o-paper-clip')
                    ->label('Файлы для скачивания')
                    ->schema([
                        Repeater::make('file')
                            ->label('')
                            ->hint('Максимум 10 файлов')
                            ->schema([
                                Hidden::make('expansion')->required(),
                                Hidden::make('size')->required(),

                                TextInput::make('title')
                                    ->label('Название файла')
                                    ->placeholder('Годовой отчет 2023.pdf')
                                    ->required()
                                    ->maxLength(255),

                                FileUpload::make('path')
                                    ->label('Выберите файл')
                                    ->helperText('Допустимы: PDF, DOCX, XLSX, PPTX, ZIP')
                                    ->hint('Макс. размер 500KB')
                                    ->required()
                                    ->acceptedFileTypes([
                                        'application/pdf',
                                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                                        'application/zip'
                                    ])
                                    ->maxSize(512000)
                                    ->disk('public')
                                    ->directory('files')
                                    ->downloadable()
                                    ->preserveFilenames()
                                    ->afterStateUpdated(function ($set, $state) {
                                        $set('expansion', $state?->getClientOriginalExtension());
                                        $set('size', ByteConverter::bytesToHuman($state?->getSize()));
                                    }),
                            ])
                            ->maxItems(10)
                            ->collapsible()
                            ->itemLabel(fn (array $state): string => $state['title'] ?? 'Новый файл'),
                    ]),

                // Карточка персоны
                Builder\Block::make('person')
                    ->icon('heroicon-o-user')
                    ->label('Карточка сотрудника')
                    ->schema([
                        TextInput::make('name')
                            ->label('ФИО')
                            ->placeholder('Иванов Иван Иванович')
                            ->required()
                            ->maxLength(255),

                        FileUpload::make('photo')
                            ->label('Фотография')
                            ->hint('Оптимальный размер 500x500px')
                            ->helperText('Автоматическая конвертация в WebP')
                            ->image()
                            ->optimize('webp')
                            ->resize(50)
                            ->disk('public')
                            ->directory('personnel')
                            ->imageEditor()
                            ->required(),

                        Repeater::make('info')
                            ->label('Характеристики')
                            ->hint('Добавьте 3-5 ключевых пунктов')
                            ->schema([
                                TextInput::make('column')
                                    ->label('Параметр')
                                    ->placeholder('Стаж работы')
                                    ->required()
                                    ->maxLength(100),

                                Textarea::make('content')
                                    ->label('Значение')
                                    ->placeholder('10 лет')
                                    ->required()
                                    ->maxLength(500),
                            ])
                            ->minItems(1)
                            ->maxItems(10)
                            ->collapsible()
                            ->itemLabel(fn (array $state): string => $state['column'] ?? 'Новый параметр'),
                    ]),

                // Этапы
                Builder\Block::make('stepper')
                    ->icon('heroicon-o-list-bullet')
                    ->label('Пошаговый процесс')
                    ->schema([
                        TextInput::make('step_name')
                            ->label('Название процесса')
                            ->placeholder('Процесс согласования')
                            ->required()
                            ->maxLength(100),

                        Repeater::make('steps')
                            ->label('Этапы')
                            ->hint('Добавьте последовательные шаги')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Шаг')
                                    ->placeholder('1. Подготовка документов')
                                    ->required()
                                    ->maxLength(100),

                                RichEditor::make('content')
                                    ->label('Описание')
                                    ->required()
                                    ->maxLength(2000),
                            ])
                            ->minItems(2)
                            ->collapsible()
                            ->itemLabel(fn (array $state): string => $state['title'] ?? 'Новый этап'),
                    ]),

                // Табы
                Builder\Block::make('tabs')
                    ->icon('heroicon-o-rectangle-stack')
                    ->label('Табы')
                    ->schema([
                        Repeater::make('tabs')
                            ->label('')
                            ->hint('Оптимально 3-5 вкладок')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Название вкладки')
                                    ->placeholder('Характеристики')
                                    ->required()
                                    ->maxLength(50),

                                RichEditor::make('content')
                                    ->label('Содержимое')
                                    ->required(),
                            ])
                            ->minItems(2)
                            ->maxItems(8)
                            ->collapsible()
                            ->itemLabel(fn (array $state): string => $state['title'] ?? 'Новая вкладка'),
                    ]),

                // Слайдер изображений
                Builder\Block::make('images')
                    ->icon('heroicon-o-photo')
                    ->label('Галерея изображений')
                    ->schema([
                        FileUpload::make('url')
                            ->label('Изображения')
                            ->hint('Оптимально 3-5 изображений')
                            ->helperText('Поддерживаются JPG, PNG, WEBP')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->minFiles(1)
                            ->maxFiles(10)
                            ->disk('public')
                            ->directory('gallery')
                            ->imageEditor()
                            ->required(),

                        TextInput::make('alt')
                            ->label('Описание для SEO')
                            ->placeholder('Наш офис в Москве')
                            ->hint('Краткое описание изображения')
                            ->maxLength(255),
                    ]),

                // Одиночное изображение
                Builder\Block::make('image')
                    ->icon('heroicon-o-photo')
                    ->label('Изображение')
                    ->schema([
                        FileUpload::make('url')
                            ->label('Выберите изображение')
                            ->helperText('Рекомендуемое соотношение 16:9')
                            ->image()
                            ->disk('public')
                            ->directory('images')
                            ->imageEditor()
                            ->required(),

                        TextInput::make('alt')
                            ->label('ALT-текст')
                            ->placeholder('Описание изображения')
                            ->required()
                            ->maxLength(255),
                    ]),

                // Видео
                Builder\Block::make('video')
                    ->icon('heroicon-o-film')
                    ->label('Видео')
                    ->schema([
                        TextInput::make('mime')
                            ->label('Формат')
                            ->readOnly(),

                        TextInput::make('title')
                            ->label('Название видео')
                            ->placeholder('Обзор продукта')
                            ->required()
                            ->maxLength(255),

                        FileUpload::make('path')
                            ->label('Видеофайл')
                            ->hint('MP4, WebM, до 50MB')
                            ->helperText('Рекомендуемое разрешение 1080p')
                            ->required()
                            ->acceptedFileTypes([
                                'video/mp4',
                                'video/webm',
                                'video/ogg',
                            ])
                            ->maxSize(51200)
                            ->disk('public')
                            ->directory('videos'),
                    ]),

                // Список новостей
                Builder\Block::make('postsList')
                    ->icon('heroicon-o-newspaper')
                    ->label('Лента новостей')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('count')
                                    ->label('Количество')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(20)
                                    ->default(5)
                                    ->required(),

                                Select::make('category')
                                    ->label('Категория')
                                    ->options(Category::all()->pluck('title', 'id'))
                                    ->searchable()
                                    ->placeholder('Все категории'),
                            ]),
                    ]),

                // Отдельная новость
                Builder\Block::make('postItem')
                    ->icon('heroicon-o-document-text')
                    ->label('Конкретная новость')
                    ->schema([
                        Select::make('post')
                            ->label('Выберите новость')
                            ->options(Post::published()->pluck('title', 'id'))
                            ->searchable()
                            ->required()
                            ->placeholder('Начните вводить название'),
                    ]),

                // Страница
                Builder\Block::make('pageItem')
                    ->icon('heroicon-o-document')
                    ->label('Ссылка на страницу')
                    ->schema([
                        Select::make('page')
                            ->label('Страница')
                            ->options(Page::visible()->pluck('title', 'id'))
                            ->searchable()
                            ->required(),
                    ]),

                // Форма
                Builder\Block::make('customForm')
                    ->icon('heroicon-o-clipboard-document')
                    ->label('Форма')
                    ->schema([
                        Select::make('form')
                            ->label('Выберите форму')
                            ->options(CustomForm::published()->pluck('title', 'form_id'))
                            ->searchable()
                            ->required(),
                    ]),

                // Ресурсы
                Builder\Block::make('pageResourceList')
                    ->icon('heroicon-o-archive-box')
                    ->label('Список ресурсов')
                    ->schema([
                        Select::make('resource')
                            ->label('Ресурс')
                            ->options(PageReferenceList::active()->pluck('title', 'slug'))
                            ->searchable()
                            ->required(),
                    ]),
            ])
            ->collapsed()
            ->blockNumbers(false)
            ->collapsible()
            ->blockPickerColumns(3)
            ->blockPickerWidth('2xl')
            ->addActionLabel('Добавить блок')
            ->addBetweenActionLabel('Вставить блок между')
            ->cloneActionLabel('Клонировать блок');
    }


}