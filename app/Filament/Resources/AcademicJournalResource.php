<?php

namespace App\Filament\Resources;

use App\Enums\CustomFormStatus;
use App\Enums\PostStatus;
use App\Filament\Resources\AcademicJournalResource\Pages;
use App\Filament\Resources\AcademicJournalResource\RelationManagers;
use App\Filament\Resources\AcademicJournalResource\RelationManagers\JournalsRelationManager;
use App\Helpers\ByteConverter;
use App\Models\AcademicJournal;
use App\Models\Category;
use App\Models\CustomForm;
use App\Models\Page;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class AcademicJournalResource extends Resource
{

    protected static ?string $navigationGroup = 'Наука';

    public static ?string $label = 'Журнал';

    protected static ?string $pluralLabel = 'Научные журналы';

    protected static ?string $model = AcademicJournal::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\Grid::make(2)->schema([
                        TextInput::make('title')->label('Заголовок')->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, string|null $state, Forms\Set $set) {
                                $set('slug', Str::slug($state));
                            }),
                        TextInput::make('slug')->label('Slug')->unique(ignoreRecord: true)->readOnly()->required(),
                    ]),
                    ]),
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Основная информация журнала')
                            ->schema([
                                Builder::make('main_info')->label('')->blocks([
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
                                ])
                                    ->collapsed()
                                    ->blockNumbers(false)
                                    ->collapsible()
                                    ->blockPickerColumns(3)
                                    ->blockPickerWidth('2xl')
                                    ->addActionLabel('Добавить новый блок'),
                            ]),
                        Tabs\Tab::make('Редакция')
                            ->schema([
                                Forms\Components\Section::make('Главный редактор')->schema([
                                    Forms\Components\Repeater::make('chief_editor')->label('')->schema([
                                        TextInput::make('name')->label('Имя'),
                                        TextInput::make('academicTitle')->label('Ученная степень'),
                                        TextInput::make('position')->label('Должность'),
                                        TextInput::make('institution')->label('Учереждение'),
                                    ])->maxItems(1)->reorderable(false),
                                ]),
                                Forms\Components\Section::make('Редакторы')->schema([
                                    Forms\Components\Repeater::make('editors')->label('')->schema([
                                        TextInput::make('name')->label('Имя'),
                                        TextInput::make('academicTitle')->label('Ученная степень'),
                                        TextInput::make('position')->label('Должность'),
                                        TextInput::make('institution')->label('Учереждение'),
                                    ])
                                        ->collapsed()
                                        ->collapsible()
                                        ->label('')
                                        ->addActionLabel('Добавить редактора'),

                                ]),
                            ]),
                        Tabs\Tab::make('Информация для авторов')
                            ->schema([
                                Builder::make('for_authors')->label('')->blocks([
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
                                ])
                                    ->collapsed()
                                    ->blockNumbers(false)
                                    ->collapsible()
                                    ->blockPickerColumns(3)
                                    ->blockPickerWidth('2xl')
                                    ->addActionLabel('Добавить новый блок'),
                            ]),
                    ])->columnSpanFull()


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            JournalsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAcademicJournals::route('/'),
            'create' => Pages\CreateAcademicJournal::route('/create'),
            'edit' => Pages\EditAcademicJournal::route('/{record}/edit'),
        ];
    }
}
