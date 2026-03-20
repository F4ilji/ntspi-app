<?php

namespace App\Filament\Resources\SliderResource\RelationManagers;

use App\Containers\Event\Models\Event;
use App\Containers\AppStructure\Models\Page;
use App\Containers\Article\Models\Post;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class SlidesRelationManager extends RelationManager
{
    protected static string $relationship = 'slides';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Быстрая настройка слайда')
                    ->description('Выберите источник данных для слайда')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('model_select')
                                    ->label('Тип контента')
                                    ->placeholder('Выберите тип контента')
                                    ->helperText('Выберите откуда брать данные для слайда')
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
                                    })
                                    ->dehydrated(false),

                                Forms\Components\Select::make('model')
                                    ->label('Выбор элемента')
                                    ->placeholder('Выберите элемент')
                                    ->helperText(function (Forms\Get $get) {
                                        if ($get('model_select') === 'Post') return 'Выберите новость';
                                        if ($get('model_select') === 'Page') return 'Выберите страницу';
                                        if ($get('model_select') === 'Event') return 'Выберите мероприятие';
                                        return 'Доступно после выбора типа контента';
                                    })
                                    ->searchable()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, string|null $state, Forms\Set $set, Forms\Get $get) {
                                        if ($get('model_select') === 'Post' && $state) {
                                            $post = Post::find($state);
                                            if ($post) {
                                                $set('title', $post->title);
                                                $relativeUrl = parse_url(route('client.post.show', $post->slug), PHP_URL_PATH);
                                                $set('link', $relativeUrl);
                                            }
                                        }
                                        if ($get('model_select') === 'Page' && $state) {
                                            $page = Page::find($state);
                                            if ($page) {
                                                $set('title', $page->title);
                                                $set('link', $page->path);
                                            }
                                        }
                                        if ($get('model_select') === 'Event' && $state) {
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
                                            return Post::where('status', 'published')->orderBy('created_at', 'desc')->pluck('title', 'id');
                                        }
                                        if ($get('model_select') === 'Page') {
                                            return Page::whereNotNull('title')->pluck('title', 'id');
                                        }
                                        if ($get('model_select') === 'Event') {
                                            return Event::all()->pluck('title', 'id');
                                        }
                                        return [];
                                    })
                                    ->disabled(fn (Forms\Get $get) => empty($get('model_select')))
                                    ->dehydrated(false),
                            ]),
                    ]),

                Section::make('Контент слайда')
                    ->schema([
                        Section::make('Текстовая часть')
                            ->collapsible()
                            ->schema([
                                TextInput::make('title')
                                    ->label('Заголовок слайда')
                                    ->placeholder('Введите заголовок слайда')
                                    ->maxLength(255),

                                Textarea::make('content')
                                    ->label('Описание слайда')
                                    ->placeholder('Введите текст слайда')
                                    ->maxLength(1000),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        ColorPicker::make('color_theme')
                                            ->label('Цвет текста')
                                            ->default('#ffffff')
                                            ->required(),

                                        ToggleButtons::make('settings.text_position')
                                            ->label('Позиция текста')
                                            ->helperText('Расположение текста на слайде')
                                            ->options([
                                                'left' => 'Слева',
                                                'center' => 'По центру',
                                                'right' => 'Справа'
                                            ])
                                            ->inline()
                                            ->grouped()
                                            ->default('left'),
                                    ]),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Toggle::make('active_button')
                                            ->label('Показывать кнопку')
                                            ->helperText('Если выключено - ссылка будет работать при клике на весь слайд')
                                            ->inline(false)
                                            ->dehydrated(false)
                                            ->default(true)
                                            ->live(),

                                        TextInput::make('settings.link_text')
                                            ->label('Текст кнопки')
                                            ->placeholder('Например: Подробнее')
                                            ->default('Читать')
                                            ->disabled(fn (Forms\Get $get) => !$get('active_button'))
                                            ->maxLength(50),
                                    ]),
                            ]),

                        Section::make('Изображение')
                            ->collapsible()
                            ->schema([
                                FileUpload::make('image.url')
                                    ->label('Изображение слайда')
                                    ->helperText('Рекомендуемое соотношение сторон: 16:9')
                                    ->image()
                                    ->optimize('webp')
                                    ->resize(50)
                                    ->disk('public')
                                    ->directory('slider-images')
                                    ->imageEditor()
                                    ->required()
                                    ->downloadable()
                                    ->openable(),

                                ToggleButtons::make('image.shading')
                                    ->label('Затемнение фона')
                                    ->helperText('Для лучшей читаемости текста')
                                    ->options([
                                        '1' => 'Нет',
                                        '0.7' => 'Слабое',
                                        '0.5' => 'Среднее',
                                        '0.3' => 'Сильное',
                                    ])
                                    ->inline()
                                    ->grouped()
                                    ->default('0.5'),
                            ]),

                        Section::make('Настройки отображения')
                            ->collapsible()
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        DateTimePicker::make('start_time')
                                            ->label('Дата начала показа')
                                            ->helperText('Когда слайд станет активным')
                                            ->native(false)
                                            ->displayFormat('d/m/Y H:i')
                                            ->seconds(false)
                                            ->default(Carbon::now()),

                                        DateTimePicker::make('end_time')
                                            ->label('Дата окончания показа')
                                            ->helperText('Когда слайд перестанет показываться')
                                            ->native(false)
                                            ->displayFormat('d/m/Y H:i')
                                            ->seconds(false)
                                            ->default(Carbon::now()->addWeeks(2))
                                    ]),

                                TextInput::make('link')
                                    ->label('Целевая ссылка')
                                    ->placeholder('URL или относительный путь')
                                    ->required()
                                    ->maxLength(255),

                                Toggle::make('is_active')
                                    ->label('Активный слайд')
                                    ->helperText('Отключите чтобы временно скрыть слайд')
                                    ->default(true)
                                    ->inline(false)
                                    ->onColor('success')
                                    ->offColor('danger'),
                            ]),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->defaultSort('sort')
            ->reorderable('sort')
            ->columns([
                Tables\Columns\TextColumn::make('sort')
                    ->label('Порядок')
                    ->sortable(),

                ImageColumn::make('image.url')
                    ->label('Изображение')
                    ->size(80),

                Tables\Columns\TextColumn::make('title')
                    ->label('Заголовок')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Активен')
                    ->onColor('success')
                    ->offColor('danger')
                    ->updateStateUsing(function ($record, $state) {
                        $record->is_active = $state;
                        $record->save();
                    }),

                Tables\Columns\TextColumn::make('start_time')
                    ->label('Начало')
                    ->date('d.m.Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_time')
                    ->label('Окончание')
                    ->date('d.m.Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_active')
                    ->label('Статус')
                    ->options([
                        true => 'Активные',
                        false => 'Неактивные',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Добавить слайд'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->tooltip('Редактировать'),

                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->tooltip('Удалить'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Удалить выбранное'),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Добавить слайд'),
            ]);
    }}