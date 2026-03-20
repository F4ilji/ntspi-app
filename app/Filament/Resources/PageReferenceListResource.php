<?php

namespace App\Filament\Resources;

use App\Containers\AppStructure\Models\Page;
use App\Containers\Article\Models\Post;
use App\Containers\Event\Models\Event;
use App\Containers\Widget\Models\PageReferenceList;
use App\Filament\Resources\PageReferenceListResource\Pages;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

use Illuminate\Support\Str;
use TomatoPHP\FilamentIcons\Components\IconPicker;

class PageReferenceListResource extends Resource
{
    protected static ?string $model = PageReferenceList::class;

    public static ?string $label = 'Список ресурсов';

    protected static ?string $pluralLabel = 'Списки ресурсов';

    protected static ?string $navigationGroup = 'Виджеты';



    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Ресурс')
                    ->description('Управление контентом ресурса')
                    ->collapsible()
                    ->schema([
                        Tabs::make('Настройки ресурса')
                            ->persistTabInQueryString()
                            ->columnSpanFull()
                            ->tabs([
                                Tabs\Tab::make('Основная информация')
                                    ->icon('heroicon-o-information-circle')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                TextInput::make('title')
                                                    ->label('Название ресурса')
                                                    ->placeholder('Введите название ресурса')
                                                    ->helperText('Это название будет отображаться в административной панели')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(function (string $operation, string|null $state, Forms\Set $set) {
                                                        $set('slug', Str::slug($state));
                                                        $set('seo.title', $state);
                                                    }),

                                                TextInput::make('slug')
                                                    ->label('URL-адрес (Slug)')
                                                    ->helperText('Автоматически генерируется из названия')
                                                    ->required()
                                                    ->unique(ignoreRecord: true)
                                                    ->readOnly()
                                                    ->maxLength(255),

                                                Toggle::make('is_active')
                                                    ->label('Активность ресурса')
                                                    ->helperText('Отключите, чтобы скрыть ресурс')
                                                    ->default(true)
                                                    ->inline(false)
                                                    ->onColor('success')
                                                    ->offColor('danger')
                                                    ->columnSpanFull(),
                                            ])
                                    ]),

                                Tabs\Tab::make('Содержание ресурса')
                                    ->icon('heroicon-o-document-text')
                                    ->schema([
                                        Repeater::make('content')
                                            ->label('Элементы ресурса')
                                            ->helperText('Добавьте и настройте элементы ресурса')
                                            ->addActionLabel('Добавить элемент')
                                            ->collapsed()
                                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Новый элемент')
                                            ->required()
                                            ->schema([
                                                Forms\Components\Section::make('Быстрая настройка')
                                                    ->description('Выберите тип и источник данных')
                                                    ->collapsible()
                                                    ->collapsed()
                                                    ->schema([
                                                        Forms\Components\Grid::make(2)
                                                            ->schema([
                                                                Forms\Components\Select::make('model_select')
                                                                    ->label('Тип данных')
                                                                    ->placeholder('Выберите тип данных')
                                                                    ->helperText('Выберите тип контента для этого элемента')
                                                                    ->options([
                                                                        'Post' => 'Новость',
                                                                        'Page' => 'Страница',
                                                                        'Event' => 'Мероприятие',
                                                                        'Custom' => 'Кастомная ссылка',
                                                                    ])
                                                                    ->live(onBlur: true)
                                                                    ->afterStateUpdated(function (string $operation, string|null $state, Forms\Set $set, Forms\Get $get) {
                                                                        if ($get('model_select') === 'Custom') {
                                                                            $set('model', null);
                                                                            $set('title', null);
                                                                            $set('content', null);
                                                                            $set('link', null);
                                                                        }
                                                                    }),

                                                                Forms\Components\Select::make('model')
                                                                    ->label('Выбор элемента')
                                                                    ->placeholder('Выберите элемент')
                                                                    ->helperText(function (Forms\Get $get) {
                                                                        if ($get('model_select') === 'Post') return 'Выберите новость';
                                                                        if ($get('model_select') === 'Page') return 'Выберите страницу';
                                                                        if ($get('model_select') === 'Event') return 'Выберите мероприятие';
                                                                        return 'Доступно после выбора типа данных';
                                                                    })
                                                                    ->searchable()
                                                                    ->live(onBlur: true)
                                                                    ->afterStateUpdated(function (string $operation, string|null $state, Forms\Set $set, Forms\Get $get) {
                                                                        if ($get('model_select') === 'Post') {
                                                                            $post = Post::find($state);
                                                                            if ($post) {
                                                                                $set('title', $post->title);
                                                                                $relativeUrl = parse_url(route('client.post.show', $post->slug), PHP_URL_PATH);
                                                                                $set('link', $relativeUrl);
                                                                            }
                                                                        }
                                                                        if ($get('model_select') === 'Page') {
                                                                            $page = Page::find($state);
                                                                            if ($page) {
                                                                                $set('title', $page->title);
                                                                                $set('link', $page->path);
                                                                            }
                                                                        }
                                                                        if ($get('model_select') === 'Event') {
                                                                            $event = Event::find($state);
                                                                            if ($event) {
                                                                                $set('title', $event->title);
                                                                                $relativeUrl = parse_url(route('client.event.show', $event->slug), PHP_URL_PATH);
                                                                                $set('link', $relativeUrl);
                                                                            }
                                                                        }
                                                                    })
                                                                    ->options(function (Forms\Get $get) {
                                                                        if ($get('model_select') === 'Post') {
                                                                            return Post::where('status', '=', 'published')->pluck('title', 'id');
                                                                        }
                                                                        if ($get('model_select') === 'Page') {
                                                                            return Page::whereNotNull('title')->pluck('title', 'id');
                                                                        }
                                                                        if ($get('model_select') === 'Event') {
                                                                            return Event::all()->pluck('title', 'id');
                                                                        }
                                                                        return [];
                                                                    })
                                                                    ->disabled(fn (Forms\Get $get) => empty($get('model_select')) || $get('model_select') === 'Custom'),
                                                            ]),
                                                    ]),

                                                TextInput::make('title')
                                                    ->label('Заголовок')
                                                    ->placeholder('Введите заголовок элемента')
                                                    ->helperText('Заголовок будет отображаться пользователям')
                                                    ->required()
                                                    ->maxLength(255),

                                                // Новый блок для выбора между изображением и иконкой
                                                Forms\Components\Section::make('Графическое представление')
                                                    ->description('Выберите тип графического представления')
                                                    ->collapsible()
                                                    ->schema([
                                                        Forms\Components\Radio::make('visual_type')
                                                            ->label('Тип представления')
                                                            ->options([
                                                                'image' => 'Изображение',
                                                                'icon' => 'Иконка',
                                                            ])
                                                            ->default('image')
                                                            ->dehydrated(false)
                                                            ->live()
                                                            ->inline(),

                                                        FileUpload::make('image')
                                                            ->label('Изображение предпросмотра')
                                                            ->helperText('Рекомендуемый формат: PNG, JPEG, JPG')
                                                            ->image()
                                                            ->optimize('webp')
                                                            ->resize(50)
                                                            ->disk('public')
                                                            ->directory('images')
                                                            ->imageEditor()
                                                            ->downloadable()
                                                            ->openable()
                                                            ->visible(fn (Forms\Get $get): bool => $get('visual_type') === 'image'),

                                                        IconPicker::make('icon')
                                                            ->label('Иконка')
                                                            ->default('heroicon-o-academic-cap')
                                                            ->helperText('Выберите иконку для отображения')
                                                            ->columns(6)
                                                            ->visible(fn (Forms\Get $get): bool => $get('visual_type') === 'icon'),
                                                    ]),

                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('link')
                                                            ->label('Ссылка')
                                                            ->placeholder('https://example.com или /path')
                                                            ->helperText('URL-адрес или относительный путь')
                                                            ->required()
                                                            ->maxLength(255),

                                                        Forms\Components\TextInput::make('link_text')
                                                            ->label('Текст кнопки')
                                                            ->placeholder('Например: Читать далее')
                                                            ->helperText('Текст для кнопки перехода')
                                                            ->default('Читать')
                                                            ->required()
                                                            ->maxLength(50),
                                                    ]),
                                            ])
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ]),
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('title')->label('Название')->sortable()->searchable(),
                TextColumn::make('created_at')->label('Дата создания')->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')->label('Активно')->sortable(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPageReferenceLists::route('/'),
            'create' => Pages\CreatePageReferenceList::route('/create'),
            'edit' => Pages\EditPageReferenceList::route('/{record}/edit'),
        ];
    }
}
