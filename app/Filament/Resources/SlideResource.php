<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SlideResource\Pages;
use App\Models\Event;
use App\Models\Page;
use App\Models\Post;
use App\Models\Slide;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Illuminate\Support\Carbon;

class SlideResource extends Resource
{
    protected static ?string $model = Slide::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Быстрая настройка слайда')->schema([
                    Forms\Components\Grid::make()->schema([
                        Forms\Components\Select::make('model_select')
                            ->name('')
                            ->label('Выбор типа данных')
                            ->options([
                                'Post' => 'Новость',
                                'Page' => 'Страница',
                                'Event' => 'Мероприятие',
                                'Custom' => 'Кастомная ссылка',
                            ])
                            ->afterStateUpdated(function (string $operation, string|null $state, Forms\Set $set, Forms\Get $get) {
                                if ($get('model_select') === 'Custom') {
                                    $set('model', null);
                                    $set('title', null);
                                    $set('content', null);
                                    $set('link', null);
                                };
                            })->live(onBlur: true),

                        Forms\Components\Select::make('model')
                            ->label('Поиск данных')
                            ->name('')
                            ->live(onBlur: true)
                            ->searchable()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, string|null $state, Forms\Set $set, Forms\Get $get) {
                                if ($get('model_select') === 'Post') {
                                    $post = Post::find($state);
                                    $set('title', $post->title);
                                    $relativeUrl = parse_url(route('client.post.show', $post->slug), PHP_URL_PATH);
                                    $set('link', $relativeUrl);
                                };
                                if ($get('model_select') === 'Page') {
                                    $page = Page::find($state);
                                    $set('title', $page->title);
                                    $set('link', $page->path);
                                };
                                if ($get('model_select') === 'Event') {
                                    $event = Event::find($state);
                                    $set('title', $event->title);
                                    $relativeUrl = parse_url(route('client.event.show', $event->slug), PHP_URL_PATH);
                                    $set('link', $relativeUrl);
                                };

                            })
                            ->options(function (Forms\Get $get) {
                                if ($get('model_select') === 'Post') {
                                    return Post::where('status', '=', 'published')->pluck('title', 'id');
                                };
                                if ($get('model_select') === 'Page') {
                                    return Page::where('title', '!=', null)->pluck('title', 'id');
                                };
                                if ($get('model_select') === 'Event') {
                                    return Event::all()->pluck('title', 'id');
                                };
                                if ($get('model_select') === 'Custom') {
                                    return [];
                                };
                            }),
                    ]),
                ]),
                Forms\Components\Section::make('Слайдер')->schema([

                    Forms\Components\Section::make('Информация слайда')->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Заголовок слайда'),
                        Forms\Components\Textarea::make('content')
                            ->label('Текст слайда'),
                        Forms\Components\Grid::make()->schema([
                            ColorPicker::make('color_theme')
                                ->label('Цвет текста')
                                ->default('#ffffff')
                                ->required(),
                            Forms\Components\ToggleButtons::make('settings.text_position')
                                ->options([
                                    'left' => 'Текст слева',
                                    'center' => 'Текст по середине',
                                    'right' => 'Текст справа'
                                ])
                                ->inline()->default('left')->grouped()
                                ->label('Позиция текста на слайде'),
                        ]),
                        Forms\Components\Grid::make()->schema([
                            Toggle::make('active_button')
                                ->label('Использовать кнопку для ссылки (Ссылка будет открываться при нажатии на слайд)')
                                ->inline(false)
                                ->default(true) // Проверяем, есть ли текст в link_text
                                ->live()
                                ->afterStateHydrated(function (Toggle $component, $state, $get) {
                                    if ($state === null && !empty($get('settings.link_text'))) {
                                        $component->state(true); // Устанавливаем значение по умолчанию
                                    }
                                })
                                ->dehydrated(false),
                            Forms\Components\TextInput::make('settings.link_text')
                                ->default('Читать')
                                ->label('Текст кнопки')
                                ->disabled(fn (Forms\Get $get) => !$get('active_button'))
                        ]),
                    ]),
                    Forms\Components\Section::make('Изображение')->schema([
                        FileUpload::make('image.url')
                            ->label('Изображение')
                            ->image()
                            ->optimize('webp')
                            ->resize(50)
                            ->disk('public')
                            ->directory('images')
                            ->imageEditor()
                            ->required(),
                        ToggleButtons::make('image.shading')->inline()->grouped()->label('Уровень затемнения изображения')->options([
                            '1' => 'Без затемнения',
                            '0.7' => 'Слабое затемнение',
                            '0.5' => 'Среднее затемнение',
                            '0.3' => 'Сильное затемнение',
                        ]),
                    ]),
                    Forms\Components\Section::make('Общая часть')->schema([
                        Forms\Components\Grid::make()->schema([
                            DateTimePicker::make('start_time')
                                ->label('Слайд начинается с')
                                ->native()
                                ->displayFormat('d/m/Y')
                                ->default(Carbon::now())
                                ->maxDate(Carbon::now()->addWeeks(2)),
                            DateTimePicker::make('end_time')
                                ->label('Слайд действует до')
                                ->native()
                                ->displayFormat('d/m/Y')
                                ->default(Carbon::now()->addWeeks(2))
                                ->minDate(Carbon::now())
                                ->maxDate(Carbon::now()->addMonth()),
                        ]),
                        Forms\Components\TextInput::make('link')
                            ->label('Ссылка кнопки')
                            ->required(),
                    ]),

                    Toggle::make('is_active')->default(true)->label('Активный слайдер')->inline(false),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\ToggleColumn::make('is_active')
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
            'index' => Pages\ListSlides::route('/'),
            'create' => Pages\CreateSlide::route('/create'),
            'edit' => Pages\EditSlide::route('/{record}/edit'),
        ];
    }
}
