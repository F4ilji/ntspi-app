<?php

namespace App\Filament\Resources;

use App\Containers\Event\Models\Event;
use App\Containers\Event\Models\EventCategory;
use App\Filament\Components\Forms\ItemForm\Pages\ContentBuilderItem;
use App\Filament\Resources\EventResource\Pages;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationGroup = 'Новости и мероприятия';
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $modelLabel = 'Мероприятие';
    protected static ?string $pluralModelLabel = 'Мероприятия';
    protected static ?string $navigationLabel = 'Мероприятия';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Мероприятие')
                    ->tabs([
                        Tabs\Tab::make('Основное')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Название мероприятия')
                                            ->placeholder('Введите название мероприятия')
                                            ->helperText('Отображается на сайте')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                                $set('slug', $state ? Str::slug($state) : null);
                                            }),

                                        TextInput::make('slug')
                                            ->label('URL-адрес')
                                            ->unique(ignoreRecord: true)
                                            ->required()
                                            ->readOnly()
                                            ->helperText('Формируется автоматически из названия')
                                            ->prefix(fn () => route('client.event.index') . '/')
                                            ->suffixAction(
                                                Action::make('copy')
                                                    ->icon('heroicon-s-clipboard-document-check')
                                                    ->action(function ($livewire, $state) {
                                                        $livewire->js(
                                                            'window.navigator.clipboard.writeText("'. route('client.event.index') . '/' . $state.'");
                    $tooltip("'.__('Copied to clipboard').'", { timeout: 1500 });'
                                                        );
                                                    })),
                                    ]),

                                Select::make('category_id')
                                    ->label('Категория')
                                    ->placeholder('Выберите категорию')
                                    ->options(EventCategory::all()->pluck('title', 'id'))
                                    ->preload()
                                    ->helperText('Для систематизации мероприятий'),

                                SpatieTagsInput::make('tags')
                                    ->label('Теги')
                                    ->placeholder('Добавьте теги')
                                    ->helperText('Для фильтрации и поиска'),

                                Toggle::make('is_online')
                                    ->label('Онлайн-формат')
                                    ->helperText('Отметьте для онлайн-мероприятий')
                                    ->default(false)
                                    ->inline(false)
                                    ->onColor('success')
                                    ->offColor('gray'),
                            ]),

                        Tabs\Tab::make('Контент')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                ContentBuilderItem::getItem('content')
                                    ->columnSpanFull(),
                            ]),

                        Tabs\Tab::make('Дата и место')
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                TextInput::make('address')
                                    ->label('Место проведения')
                                    ->placeholder('Адрес или платформа')
                                    ->helperText('Для онлайн укажите платформу (Zoom, YouTube и т.д.)')
                                    ->required()
                                    ->maxLength(255),

                                Grid::make(2)
                                    ->schema([
                                        DatePicker::make('event_date_start')
                                            ->label('Дата начала')
                                            ->native(false)
                                            ->displayFormat('d/m/Y')
                                            ->helperText('Когда начинается мероприятие')
                                            ->required()
                                            ->minDate(now())
                                            ->maxDate(now()->addYear()),

                                        TimePicker::make('event_time_start')
                                            ->label('Время начала')
                                            ->seconds(false)
                                            ->native(false)
                                            ->helperText('По местному времени')
                                            ->required(),

                                        DatePicker::make('event_date_end')
                                            ->label('Дата окончания')
                                            ->native(false)
                                            ->displayFormat('d/m/Y')
                                            ->helperText('Оставьте пустым для однодневного мероприятия')
                                            ->minDate(now())
                                            ->maxDate(now()->addYear()),
                                    ]),
                            ]),
                    ])
                    ->persistTabInQueryString()
                    ->columnSpanFull(),
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('title')
                    ->label('Название')
                    ->sortable()
                    ->searchable()
                    ->limit(30),

                TextColumn::make('event_date_start')
                    ->label('Дата начала')
                    ->date('d.m.Y H:i')
                    ->sortable(),

                IconColumn::make('is_online')
                    ->label('Онлайн')
                    ->boolean()
                    ->trueIcon('heroicon-o-globe-alt')
                    ->falseIcon('heroicon-o-map-pin'),

                TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Категория')
                    ->relationship('category', 'title'),

                Tables\Filters\Filter::make('is_online')
                    ->label('Только онлайн')
                    ->query(fn ($query) => $query->where('is_online', true)),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->tooltip('Редактировать'),

                Tables\Actions\Action::make('view')
                    ->icon('heroicon-o-eye')
                    ->tooltip('Просмотреть на сайте')
                    ->url(fn (Event $record) => route('client.event.show', $record->slug))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Удалить выбранное')
                        ->icon('heroicon-o-trash'),
                ]),
            ])
            ->defaultSort('event_date_start', 'desc')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Добавить мероприятие'),
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
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}