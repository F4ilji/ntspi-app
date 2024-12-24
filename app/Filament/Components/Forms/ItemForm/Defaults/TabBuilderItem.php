<?php

namespace App\Filament\Components\Forms\ItemForm\Pages;

use App\Enums\CustomFormStatus;
use App\Enums\PostStatus;
use App\Helpers\ByteConverter;
use App\Models\Category;
use App\Models\CustomForm;
use App\Models\Page;
use App\Models\PageReferenceList;
use App\Models\Post;
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

class ContentBuilderItem
{
    public static function getItem()
    {
        return
            Builder\Block::make('tabs')
                ->label('Вкладки')
                ->schema([
                    Forms\Components\Repeater::make('tab')->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)->columnSpanFull(),
                        Builder::make('content')->label('')->blocks([
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
                                    TinyEditor::make('content')
                                        ->label('')
                                        ->profile('test')
                                        ->required(),
                                ])->label('Текст'),
                            Builder\Block::make('files')
                                ->label('Файл(-ы)')
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
                                ->label('Персона')
                                ->schema([
                                    TextInput::make('name')
                                        ->label('Имя')
                                        ->required()
                                        ->maxLength(255),
                                    FileUpload::make('photo')
                                        ->label('Фотография')
                                        ->image()
                                        ->optimize('webp')
                                        ->resize(50)
                                        ->disk('public')
                                        ->directory('images')
                                        ->imageEditor(),
                                    Forms\Components\Repeater::make('info')->schema([
                                        TextInput::make('column')
                                            ->label('Название колонки')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\Textarea::make('content')
                                            ->label('Содержание')
                                            ->required()
                                            ->maxLength(1000),
                                    ])->minItems(1)->label('Информация о персоне'),
                                ]),
                            Builder\Block::make('stepper')
                                ->label('Строитель этапов')
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
                                ->label('Видео (Не стабильно)')
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
                            Builder\Block::make('pageResourceList')
                                ->schema([
                                    Select::make('resource')
                                        ->options(PageReferenceList::query()->where('is_active', true)->pluck('title', 'slug'))
                                        ->searchable()
                                        ->required(),
                                ])->label('Ресурсы'),
                        ])
                            ->collapsed()
                            ->blockNumbers(false)
                            ->collapsible()
                            ->blockPickerColumns(3)
                            ->blockPickerWidth('2xl')
                            ->addActionLabel('Добавить новый блок'),
                    ])->minItems(1),
                ]);
    }


}