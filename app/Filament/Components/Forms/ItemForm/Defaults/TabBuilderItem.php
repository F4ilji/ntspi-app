<?php

namespace App\Filament\Components\Forms\ItemForm\Defaults;

use App\Enums\CustomFormStatus;
use App\Enums\PostStatus;
use App\Helpers\ByteConverter;
use App\Models\Category;
use App\Models\ContactWidget;
use App\Models\CustomForm;
use App\Models\Page;
use App\Models\PageReferenceList;
use App\Models\Post;
use App\Models\Slider;
use Filament\Forms;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class TabBuilderItem
{
    public static function getItem()
    {
        return Builder::make('content')
            ->label('Содержимое вкладки')
            ->blocks([
                Builder\Block::make('heading')
                    ->label('Заголовок')
                    ->icon('heroicon-o-hashtag')
                    ->schema([
                        TextInput::make('id')
                            ->hidden()
                            ->integer()
                            ->default(rand(2335235, 324634264263426)),
                        TextInput::make('content')
                            ->label('Текст заголовка')
                            ->placeholder('Введите текст заголовка')
                            ->helperText('Основной заголовок раздела')
                            ->live(onBlur: true)
                            ->required()
                            ->maxLength(255),
                    ]),

                Builder\Block::make('paragraph')
                    ->label('Текст')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Toggle::make('seo_active')
                            ->label('Использовать блок как SEO-текст')
                            ->helperText('Этот текст будет использоваться для SEO-оптимизации')
                            ->live(onBlur: true)
                            ->required()
                            ->disabled(function ($state, Forms\Get $get) {
                                $data = $get('../../');
                                return self::findSeoActive($data) && !$state;
                            })
                            ->dehydrated(),
                        TinyEditor::make('content')
                            ->label('Текст')
                            ->placeholder('Начните вводить текст...')
                            ->profile('test')
                            ->required()
                            ->helperText('Основное текстовое содержимое блока'),
                    ]),

                Builder\Block::make('files')
                    ->label('Файлы')
                    ->icon('heroicon-o-paper-clip')
                    ->schema([
                        Forms\Components\Repeater::make('file')
                            ->label('Файлы')
                            ->helperText('Загрузите один или несколько файлов')
                            ->schema([
                                Hidden::make('expansion')->required(),
                                Hidden::make('size')->required(),
                                TextInput::make('title')
                                    ->label('Название файла')
                                    ->placeholder('Введите название файла')
                                    ->required()
                                    ->maxLength(255)
                                    ->autofocus()
                                    ->helperText('Это название будет отображаться пользователям'),
                                FileUpload::make('path')
                                    ->label('Файл')
                                    ->required()
                                    ->helperText('Поддерживаются PDF, Word, Excel, PowerPoint и ZIP файлы (макс. 500KB)')
                                    ->getUploadedFileNameForStorageUsing(
                                        fn (TemporaryUploadedFile $file): string =>
                                        str(Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '-' . Carbon::now()->timestamp) . '.' . $file->getClientOriginalExtension())
                                    )
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
                                    ->afterStateUpdated(function ($set, $state) {
                                        $set('expansion', $state?->getClientOriginalExtension());
                                        $set('size', ByteConverter::bytesToHuman($state?->getSize()));
                                    })
                                    ->visibility('public')
                                    ->preserveFilenames()
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                            ->collapsible()
                            ->cloneable()
                            ->grid(2),
                    ]),

                Builder\Block::make('person')
                    ->label('Персона')
                    ->icon('heroicon-o-user')
                    ->schema([
                        TextInput::make('name')
                            ->label('Имя персоны')
                            ->placeholder('Введите имя')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Полное имя персоны'),
                        FileUpload::make('photo')
                            ->label('Фотография')
                            ->image()
                            ->helperText('Рекомендуемый формат: WebP')
                            ->optimize('webp')
                            ->resize(50)
                            ->disk('public')
                            ->directory('images')
                            ->imageEditor()
                            ->required()
                            ->downloadable()
                            ->openable(),
                        Forms\Components\Repeater::make('info')
                            ->label('Дополнительная информация')
                            ->helperText('Добавьте характеристики персоны')
                            ->schema([
                                TextInput::make('column')
                                    ->label('Название характеристики')
                                    ->placeholder('Например: Должность')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('content')
                                    ->label('Значение')
                                    ->placeholder('Например: Главный инженер')
                                    ->required()
                                    ->maxLength(1000)
                                    ->columnSpanFull(),
                            ])
                            ->minItems(1)
                            ->grid(2)
                            ->collapsible()
                            ->cloneable()
                            ->itemLabel(fn (array $state): ?string => $state['column'] ?? null),
                    ]),

                Builder\Block::make('stepper')
                    ->label('Этапы')
                    ->icon('heroicon-o-list-bullet')
                    ->schema([
                        TextInput::make('step_name')
                            ->label('Название процесса')
                            ->placeholder('Например: Процесс оформления')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Общее название для всех шагов'),
                        Forms\Components\Repeater::make('steps')
                            ->label('Шаги')
                            ->helperText('Добавьте шаги процесса')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Название шага')
                                    ->placeholder('Например: Шаг 1')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                RichEditor::make('content')
                                    ->label('Описание шага')
                                    ->required()
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'link',
                                        'orderedList',
                                        'bulletList',
                                    ]),
                            ])
                            ->minItems(1)
                            ->collapsible()
                            ->cloneable()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
                    ]),

                Builder\Block::make('images')
                    ->label('Слайдер изображений')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        FileUpload::make('url')
                            ->label('Изображения')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->maxFiles(5)
                            ->disk('public')
                            ->directory('images')
                            ->imageEditor()
                            ->required()
                            ->helperText('Максимум 5 изображений. Можно перетаскивать для изменения порядка'),
                        TextInput::make('alt')
                            ->label('Описание изображений')
                            ->placeholder('Необязательно')
                            ->helperText('Используется для SEO и доступности'),
                    ]),

                Builder\Block::make('image')
                    ->label('Изображение')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        FileUpload::make('url')
                            ->label('Изображение')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->maxFiles(5)
                            ->disk('public')
                            ->directory('images')
                            ->imageEditor()
                            ->required()
                            ->helperText('Можно загрузить до 5 изображений'),
                        TextInput::make('alt')
                            ->label('Альтернативный текст')
                            ->placeholder('Необязательно')
                            ->helperText('Описание изображения для SEO'),
                    ]),

                Builder\Block::make('video')
                    ->label('Видео')
                    ->icon('heroicon-o-film')
                    ->schema([
                        TextInput::make('mime')
                            ->label('Тип видео')
                            ->readOnly()
                            ->helperText('Определяется автоматически'),
                        TextInput::make('title')
                            ->label('Название видео')
                            ->placeholder('Введите название видео')
                            ->required()
                            ->maxLength(255)
                            ->autofocus()
                            ->helperText('Это название будет отображаться перед видео'),
                        FileUpload::make('path')
                            ->label('Видеофайл')
                            ->required()
                            ->acceptedFileTypes([
                                'video/mp4',
                                'video/quicktime',
                                'video/x-msvideo',
                                'video/x-ms-wmv',
                                'video/avi',
                                'video/webm',
                                'video/ogg',
                                'video/3gpp',
                                'video/3gpp2',
                                'video/x-m4v',
                            ])
                            ->disk('public')
                            ->directory('videos')
                            ->helperText('Поддерживаются популярные видеоформаты (MP4, MOV, AVI и др.)')
                            ->afterStateUpdated(fn (callable $set, $state) => $set('mime', $state?->getMimeType())),
                    ]),

                Builder\Block::make('postsList')
                    ->label('Список новостей')
                    ->icon('heroicon-o-newspaper')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                TextInput::make('count')
                                    ->label('Количество записей')
                                    ->integer()
                                    ->minValue(1)
                                    ->maxValue(20)
                                    ->default(5)
                                    ->helperText('От 1 до 20 записей'),
                                Select::make('category')
                                    ->label('Категория')
                                    ->options(Category::all()->pluck('title', 'id'))
                                    ->searchable()
                                    ->helperText('Выберите категорию или оставьте пустым для всех'),
                            ]),
                    ]),

                Builder\Block::make('postItem')
                    ->label('Конкретная новость')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Select::make('post')
                            ->label('Новость')
                            ->options(Post::query()->where('status', PostStatus::PUBLISHED)->pluck('title', 'id'))
                            ->searchable()
                            ->required()
                            ->helperText('Выберите опубликованную новость'),
                    ]),

                Builder\Block::make('pageItem')
                    ->label('Конкретная страница')
                    ->icon('heroicon-o-document')
                    ->schema([
                        Select::make('page')
                            ->label('Страница')
                            ->options(Page::query()->where('title', '!=', null)->where('is_visible', true)->pluck('title', 'id'))
                            ->searchable()
                            ->required()
                            ->helperText('Выберите видимую страницу'),
                    ]),

                Builder\Block::make('customForm')
                    ->label('Пользовательская форма')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->schema([
                        Select::make('form')
                            ->label('Форма')
                            ->options(CustomForm::query()->where('status', CustomFormStatus::PUBLISHED)->pluck('title', 'form_id'))
                            ->searchable()
                            ->required()
                            ->helperText('Выберите опубликованную форму'),
                    ]),

                Builder\Block::make('pageResourceList')
                    ->label('Ресурсы')
                    ->icon('heroicon-o-archive-box')
                    ->schema([
                        Select::make('resource')
                            ->label('Ресурс')
                            ->options(PageReferenceList::query()->where('is_active', true)->pluck('title', 'slug'))
                            ->searchable()
                            ->required()
                            ->helperText('Выберите активный ресурс'),
                    ]),

                Builder\Block::make('contact')
                    ->label('Контакты')
                    ->icon('heroicon-o-phone')
                    ->schema([
                        Select::make('contact')
                            ->label('Виджет контактов')
                            ->options(ContactWidget::query()->where('is_active', true)->pluck('title', 'slug'))
                            ->searchable()
                            ->required()
                            ->helperText('Выберите активный виджет контактов'),
                    ]),

                Builder\Block::make('slider')
                    ->label('Слайдер')
                    ->icon('heroicon-o-presentation-chart-line')
                    ->schema([
                        Select::make('slider')
                            ->label('Слайдер')
                            ->options(Slider::query()->whereHas('slides')->where('is_active', true)->pluck('title', 'slug'))
                            ->searchable()
                            ->required()
                            ->helperText('Выберите активный слайдер с изображениями'),
                    ]),
            ])
            ->collapsed()
            ->blockPickerColumns(3)
            ->blockPickerWidth('2xl')
            ->blockNumbers(false)
            ->collapsible()
            ->addActionLabel('Добавить блок в вкладку');
    }


}