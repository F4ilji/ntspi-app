<?php

namespace App\Filament\Resources;

use App\Containers\Widget\Models\ContactWidget;
use App\Filament\Resources\ContactWidgetResource\Pages;
use Filament\Forms;
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

class ContactWidgetResource extends Resource
{
    protected static ?string $model = ContactWidget::class;

    protected static ?string $pluralLabel = 'Контактная информация';


    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Виджеты';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Ресурс')
                    ->description('Настройка контактных ресурсов')
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
                                                    ->helperText('Это название будет отображаться в интерфейсе')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(function (string $operation, string|null $state, Forms\Set $set) {
                                                        $set('slug', Str::slug($state));
                                                        $set('seo.title', $state);
                                                    })
                                                    ->columnSpan(1),

                                                TextInput::make('slug')
                                                    ->label('URL-адрес (Slug)')
                                                    ->helperText('Автоматически генерируется из названия')
                                                    ->hintIcon('heroicon-o-information-circle', tooltip: 'Изменить можно только вручную')
                                                    ->unique(ignoreRecord: true)
                                                    ->readOnly()
                                                    ->required()
                                                    ->columnSpan(1),

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
                                            ->label('Структура ресурса')
                                            ->helperText('Добавьте столбцы с контактной информацией')
                                            ->addActionLabel('Добавить столбец')
                                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Новый столбец')
                                            ->collapsible()
                                            ->cloneable()
                                            ->grid(2)
                                            ->schema([
                                                TextInput::make('title')
                                                    ->label('Заголовок столбца')
                                                    ->placeholder('Например: Контакты')
                                                    ->helperText('Основной заголовок для группы контактов')
                                                    ->required()
                                                    ->maxLength(255),

                                                Repeater::make('items')
                                                    ->label('Контактные блоки')
                                                    ->helperText('Добавьте контактные блоки в этот столбец')
                                                    ->addActionLabel('Добавить контактный блок')
                                                    ->itemLabel(fn (array $state): ?string => $state['header'] ?? 'Новый контакт')
                                                    ->collapsible()
                                                    ->cloneable()
                                                    ->schema([
                                                        TextInput::make('header')
                                                            ->label('Заголовок контакта')
                                                            ->placeholder('Например: Телефон')
                                                            ->helperText('Название контактной информации')
                                                            ->required()
                                                            ->maxLength(255),

                                                        Repeater::make('details')
                                                            ->label('Детали контакта')
                                                            ->helperText('Добавьте контактные данные')
                                                            ->addActionLabel('Добавить деталь')
                                                            ->collapsible()
                                                            ->cloneable()
                                                            ->schema([
                                                                Forms\Components\Grid::make(2)
                                                                    ->schema([
                                                                        TextInput::make('content')
                                                                            ->label('Значение')
                                                                            ->placeholder('Например: +7 (123) 456-78-90')
                                                                            ->helperText('Основная контактная информация')
                                                                            ->columnSpanFull()
                                                                            ->required(),

                                                                        TextInput::make('url')
                                                                            ->label('Ссылка')
                                                                            ->placeholder('https://example.com')
                                                                            ->helperText('Необязательная ссылка, связанная с контактом')
                                                                            ->url()
                                                                            ->columnSpanFull(),
                                                                    ])
                                                            ])
                                                    ])
                                            ])
                                            ->required(),
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
            'index' => Pages\ListContactWidgets::route('/'),
            'create' => Pages\CreateContactWidget::route('/create'),
            'edit' => Pages\EditContactWidget::route('/{record}/edit'),
        ];
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
        ];
    }
}
