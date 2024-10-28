<?php

namespace App\Filament\Components\Forms;

use App\Enums\CustomFormStatus;
use App\Enums\PostStatus;
use App\Filament\Components\Forms\ItemForm\Pages\ContentBuilderItem;
use App\Helpers\ByteConverter;
use App\Models\Category;
use App\Models\CustomForm;
use App\Models\Page;
use App\Models\PageReferenceList;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;
use Symfony\Component\Finder\Finder;

class PostForm
{

    private static function findSeoActive(array $data) : bool
    {
        $bool = false;

        foreach ($data as $item) {
            if ($item['type'] !== 'paragraph') {
                continue;
            }
            if ($item['data']['seo_active'] === true) {
                $bool = true;
                break;
            }
        }
        return $bool;
    }
    public static function getForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Tabs::make('Tabs')
                            ->tabs([
                                Tabs\Tab::make('Основная информация')
                                    ->schema([
                                        Forms\Components\Grid::make(2)->schema([
                                            TextInput::make('title')->label('Заголовок')->required()
                                                ->live(onBlur: true)
                                                ->afterStateUpdated(function (string $operation, string|null $state, Forms\Set $set) {
                                                    $set('slug', Str::slug($state));
                                                    $set('seo.title', $state);
                                                }),
                                            TextInput::make('slug')->label('Slug')->unique(ignoreRecord: true)->readOnly()->required(),
                                        ]),
                                        Select::make('status')->options(PostStatus::class)
                                            ->label('Статус')->required()
                                            ->disableOptionWhen(fn (string $value): bool =>
                                                $value == PostStatus::PUBLISHED->value && !auth()->user()->can('publish_post')
                                            )
                                            ->default(PostStatus::VERIFICATION),
                                        Select::make('category_id')
                                            ->options(Category::all()->pluck('title', 'id'))
                                            ->preload()
                                            ->label('Категория'),
                                        SpatieTagsInput::make('tags')->label('Тэги'),
                                        Forms\Components\TagsInput::make('authors')
                                            ->label('Авторы')->placeholder('Добавить автора'),
                                        Section::make('Отложенная публикация')->schema([
                                            Grid::make(2)->schema([
                                                Toggle::make('publish_setting.publish_after')
                                                    ->label('Включить')
                                                    ->inline(false)
                                                    ->default(false)
                                                    ->live(),
                                                DateTimePicker::make('publish_setting.publish_at')
                                                    ->label('Дата публикации')
                                                    ->native()
                                                    ->displayFormat('d/m/Y')

                                                    ->required(fn (Forms\Get $get) => $get('publish_setting.publish_after'))
                                                    ->disabled(fn (Forms\Get $get) => !$get('publish_setting.publish_after'))
                                                    ->minDate(Carbon::now()->subWeek())
                                                    ->maxDate(Carbon::now()->addMonth()),
                                            ]),
                                        ]),
                                        Section::make('Публикация в сервисах')->schema([
                                            Forms\Components\Grid::make()->schema([
                                                Toggle::make('publication.vk')->label('Публикация в VK')->default(true),
                                                Toggle::make('publication.telegram')->label('Публикация в Telegram')->default(true),
                                            ]),
                                        ]),
                                    ]),
                                Tabs\Tab::make('Содержание новости')
                                    ->schema([
                                        Builder::make('content')->label('')->blocks([
                                            Builder\Block::make('heading')->label('Заголовок')
                                                ->schema([
                                                    TextInput::make('id')->hidden()->integer()->default(rand(2335235,324634264263426)),
                                                    TextInput::make('content')
                                                        ->label('')
                                                        ->live(onBlur: true)
                                                        ->afterStateUpdated(function (string $operation, string|null $state, Forms\Set $set, $get) {
                                                        }),
                                                ]),
                                            Builder\Block::make('paragraph')->label('Текст')
                                                ->schema([
                                                    Toggle::make('seo_active')->label('Использовать блок как seo')
                                                        ->live(onBlur: true)
                                                        ->required()
                                                        ->disabled(function ($state, Forms\Get $get) {
                                                            $data = $get('../../');
                                                            return self::findSeoActive($data) && !$state;
                                                        })
                                                        ->dehydrated(),
                                                    RichEditor::make('content')
                                                        ->toolbarButtons([
                                                            'blockquote',
                                                            'bold',
                                                            'bulletList',
                                                            'italic',
                                                            'link',
                                                            'orderedList',
                                                            'redo',
                                                            'strike',
                                                            'underline',
                                                            'undo',
                                                        ])
                                                        ->label(''),
                                                ])->live(onBlur: true),
                                            Builder\Block::make('files')
                                                ->schema([
                                                    Forms\Components\Repeater::make('file')->schema([
                                                        Hidden::make('expansion')->required(),
                                                        Hidden::make('size')->required(),
                                                        TextInput::make('title')
                                                            ->required()
                                                            ->maxLength(255)
                                                            ->autofocus(),
                                                        FileUpload::make('path')
                                                            ->required()
                                                            ->getUploadedFileNameForStorageUsing(
                                                                fn (TemporaryUploadedFile $file): string =>
                                                                str(Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '-' . Carbon::now()->timestamp ) . '.' . $file->getClientOriginalExtension())
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
                                                    ]),
                                                ]),
                                            Builder\Block::make('person')
                                                ->schema([
                                                    TextInput::make('name')
                                                        ->label('Имя')
                                                        ->required()
                                                        ->maxLength(255),
                                                    FileUpload::make('photo')
                                                        ->label('Фотография')
                                                        ->image()
                                                        ->disk('public')
                                                        ->directory('images')
                                                        ->imageEditor(),
                                                    Forms\Components\Repeater::make('info')->schema([
                                                        Forms\Components\Grid::make(2)->schema([
                                                            TextInput::make('column')
                                                                ->required()
                                                                ->maxLength(255),
                                                            TextInput::make('content')
                                                                ->required()
                                                                ->maxLength(255),
                                                        ]),
                                                    ])->minItems(1),
                                                ]),
                                            Builder\Block::make('stepper')
                                                ->schema([
                                                    TextInput::make('step_name')
                                                        ->label('Название шага')
                                                        ->required()
                                                        ->maxLength(255),
                                                    Forms\Components\Repeater::make('steps')->schema([
                                                        TextInput::make('title')
                                                            ->required()
                                                            ->maxLength(255)->columnSpanFull(),
                                                        RichEditor::make('content')->required(),
                                                    ])->minItems(1),
                                                ]),
                                            Builder\Block::make('tabs')
                                                ->schema([
                                                    Forms\Components\Repeater::make('tab')->schema([
                                                        TextInput::make('title')
                                                            ->required()
                                                            ->maxLength(255)->columnSpanFull(),
                                                        \Filament\Forms\Components\Builder::make('content')->label('')->blocks([
                                                            Builder\Block::make('heading')->label('Заголовок')
                                                                ->schema([
                                                                    TextInput::make('id')->hidden()->integer()->default(rand(2335235,324634264263426)),
                                                                    TextInput::make('content')
                                                                        ->label('')
                                                                        ->live(onBlur: true)
                                                                        ->afterStateUpdated(function (string $operation, string $state, Forms\Set $set, $get) {
                                                                        }),
                                                                ]),
                                                            Builder\Block::make('paragraph')
                                                                ->schema([
                                                                    RichEditor::make('content')
                                                                        ->toolbarButtons([
                                                                            'blockquote',
                                                                            'bold',
                                                                            'bulletList',
                                                                            'italic',
                                                                            'link',
                                                                            'orderedList',
                                                                            'redo',
                                                                            'strike',
                                                                            'underline',
                                                                            'undo',
                                                                        ])
                                                                        ->label(''),
                                                                ])->label('Текст'),
                                                            Builder\Block::make('files')
                                                                ->schema([
                                                                    Forms\Components\Repeater::make('file')->schema([
                                                                        TextInput::make('title')
                                                                            ->required()
                                                                            ->maxLength(255)
                                                                            ->autofocus(),
                                                                        FileUpload::make('path')
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
                                                                            ->visibility('public')
                                                                    ]),
                                                                ]),
                                                            Builder\Block::make('person')
                                                                ->schema([
                                                                    TextInput::make('name')
                                                                        ->label('Имя')
                                                                        ->required()
                                                                        ->maxLength(255),
                                                                    FileUpload::make('photo')
                                                                        ->label('Фотография')
                                                                        ->image()
                                                                        ->disk('public')
                                                                        ->directory('images')
                                                                        ->imageEditor(),
                                                                    Forms\Components\Repeater::make('info')->schema([
                                                                        Forms\Components\Grid::make(2)->schema([
                                                                            TextInput::make('column')
                                                                                ->required()
                                                                                ->maxLength(255),
                                                                            TextInput::make('content')
                                                                                ->required()
                                                                                ->maxLength(255),
                                                                        ]),
                                                                    ])->minItems(1),
                                                                ]),
                                                            Builder\Block::make('stepper')
                                                                ->schema([
                                                                    TextInput::make('step_name')
                                                                        ->label('Название шага')
                                                                        ->required()
                                                                        ->maxLength(255),
                                                                    Forms\Components\Repeater::make('steps')->schema([
                                                                        TextInput::make('title')
                                                                            ->required()
                                                                            ->maxLength(255)->columnSpanFull(),
                                                                        RichEditor::make('content')->required(),
                                                                    ])->minItems(1),
                                                                ]),
                                                            Builder\Block::make('images')
                                                                ->schema([
                                                                    FileUpload::make('url')
                                                                        ->label('Изображение(-я)')
                                                                        ->image()
                                                                        ->multiple()
                                                                        ->reorderable()
                                                                        ->maxFiles(5)
                                                                        ->disk('public')
                                                                        ->directory('images')
                                                                        ->imageEditor()
                                                                        ->required(),
                                                                    TextInput::make('alt')
                                                                        ->label('Описание')
                                                                        ->placeholder('Необязяательно')
                                                                ])->label('Слайдер изображений'),
                                                            Builder\Block::make('image')
                                                                ->schema([
                                                                    FileUpload::make('url')
                                                                        ->label('Изображение(-я)')
                                                                        ->image()
                                                                        ->multiple()
                                                                        ->reorderable()
                                                                        ->maxFiles(5)
                                                                        ->disk('public')
                                                                        ->directory('images')
                                                                        ->imageEditor()
                                                                        ->required(),
                                                                    TextInput::make('alt')
                                                                        ->label('Описание')
                                                                        ->placeholder('Необязяательно')
                                                                ])->label('Изображение'),
                                                            Builder\Block::make('video')
                                                                ->schema([
                                                                    TextInput::make('mime')->readOnly(),
                                                                    TextInput::make('title')
                                                                        ->required()
                                                                        ->maxLength(255)
                                                                        ->autofocus(),
                                                                    FileUpload::make('path')
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
                                                                        ->afterStateUpdated(fn (callable $set, $state) => $set('mime', $state?->getMimeType())),
                                                                ]),
                                                            Builder\Block::make('postsList')
                                                                ->schema([
                                                                    Forms\Components\Grid::make(2)->schema([
                                                                        TextInput::make('count')
                                                                            ->label('Количество запией')
                                                                            ->integer(),
                                                                        Select::make('category')
                                                                            ->options(Category::all()->pluck('title', 'id'))
                                                                    ]),
                                                                ])->label('Список новостей'),
                                                        ])
                                                            ->collapsed()
                                                            ->blockNumbers(false)
                                                            ->collapsible()
                                                            ->addActionLabel('Добавить новый блок'),
                                                    ])->minItems(1),
                                                ]),
                                            Builder\Block::make('images')
                                                ->schema([
                                                    FileUpload::make('url')
                                                        ->label('Изображение(-я)')
                                                        ->image()
                                                        ->multiple()
                                                        ->reorderable()
                                                        ->maxFiles(5)
                                                        ->disk('public')
                                                        ->directory('images')
                                                        ->imageEditor()
                                                        ->required(),
                                                    TextInput::make('alt')
                                                        ->label('Описание')
                                                        ->placeholder('Необязяательно')
                                                ])->label('Слайдер изображений'),
                                            Builder\Block::make('image')
                                                ->schema([
                                                    FileUpload::make('url')
                                                        ->label('Изображение(-я)')
                                                        ->image()
                                                        ->multiple()
                                                        ->reorderable()
                                                        ->maxFiles(5)
                                                        ->disk('public')
                                                        ->directory('images')
                                                        ->imageEditor()
                                                        ->required(),
                                                    TextInput::make('alt')
                                                        ->label('Описание')
                                                        ->placeholder('Необязяательно')
                                                ])->label('Изображение'),
                                            Builder\Block::make('video')
                                                ->schema([
                                                    TextInput::make('mime')->readOnly(),
                                                    TextInput::make('title')
                                                        ->required()
                                                        ->maxLength(255)
                                                        ->autofocus(),
                                                    FileUpload::make('path')
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
                                                        ->afterStateUpdated(fn (callable $set, $state) => $set('mime', $state?->getMimeType())),
                                                ]),
                                            Builder\Block::make('postsList')
                                                ->schema([
                                                    Forms\Components\Grid::make(2)->schema([
                                                        TextInput::make('count')
                                                            ->label('Количество запией')
                                                            ->integer(),
                                                        Select::make('category')
                                                            ->options(Category::all()->pluck('title', 'id'))
                                                    ]),
                                                ])->label('Список новостей'),
                                            Builder\Block::make('postItem')
                                                ->schema([
                                                    Select::make('post')
                                                        ->options(Post::query()->where('status', PostStatus::PUBLISHED)->pluck('title', 'id'))
                                                        ->searchable()
                                                        ->required(),
                                                ])->label('Новость'),
                                            Builder\Block::make('pageItem')
                                                ->schema([
                                                    Select::make('page')
                                                        ->options(Page::query()->where('title', '!=' , null)->where('is_visible', true)->pluck('title', 'id'))
                                                        ->searchable()
                                                        ->required(),
                                                ])->label('Страница'),
                                            Builder\Block::make('customForm')
                                                ->schema([
                                                    Select::make('form')
                                                        ->options(CustomForm::query()->where('status', CustomFormStatus::PUBLISHED)->pluck('title', 'form_id'))
                                                        ->searchable()
                                                        ->required(),
                                                ])->label('Форма'),
                                            Builder\Block::make('PageResourceList')
                                                ->schema([
                                                    Select::make('resource')
                                                        ->options(PageReferenceList::query()->where('is_active', true)->pluck('title', 'slug'))
                                                        ->searchable()
                                                        ->required(),
                                                ])->label('Ресурсы'),
                                        ])
                                            ->collapsed()
                                            ->required()
                                            ->blockNumbers(false)
                                            ->collapsible()
                                            ->blockPickerColumns(3)
                                            ->blockPickerWidth('2xl')
                                            ->addActionLabel('Добавить новый блок'),
                                    ]),
                                Tabs\Tab::make('Изображения')
                                    ->schema([
                                        FileUpload::make('preview')->label('Превью новости')
                                            ->image()
                                            ->optimize('webp')
                                            ->resize(50)
                                            ->imageEditor()
                                            ->directory('images'),
                                        FileUpload::make('images')->label('Альбом')
                                            ->image()
                                            ->optimize('jpg')
                                            ->resize(30)
                                            ->imageEditor()
                                            ->panelLayout('grid')
                                            ->reorderable()
                                            ->imageEditor()
                                            ->multiple()
                                            ->directory('images'),
                                    ]),
                            ]),
                    ])
            ]);
    }
}