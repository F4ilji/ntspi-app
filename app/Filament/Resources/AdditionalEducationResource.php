<?php

namespace App\Filament\Resources;

use App\Enums\FormEducation;
use App\Filament\Components\Forms\ItemForm\Pages\ContentBuilderItem;
use App\Filament\Resources\AdditionalEducationResource\Pages;
use App\Models\AdditionalEducation;
use App\Models\AdditionalEducationCategory;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class AdditionalEducationResource extends Resource
{
    protected static ?string $model = AdditionalEducation::class;

    protected static ?string $navigationGroup = 'Образование';

    public static ?string $label = 'Дополнительное образование';

    protected static ?string $pluralLabel = 'Дополнительное образование';
    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Программа ДПО')
                    ->persistTabInQueryString()
                    ->columnSpanFull()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Основные данные')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make('Общая информация')
                                    ->description('Основные сведения о программе')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('title')
                                                    ->label('Название программы')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->placeholder('Например: "Цифровые технологии в управлении"')
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(function (string $operation, ?string $state, Forms\Set $set) {
                                                        $set('slug', Str::slug($state));
                                                    })
                                                    ->helperText('Полное официальное название программы'),

                                                TextInput::make('slug')
                                                    ->label('URL-адрес')
                                                    ->required()
                                                    ->readonly()
                                                    ->maxLength(255)
                                                    ->unique(ignoreRecord: true)
                                                    ->helperText('Человеко-понятный URL для страницы программы'),
                                            ]),

                                        Select::make('category_id')
                                            ->label('Категория')
                                            ->options(AdditionalEducationCategory::where('is_active', true)->pluck('title', 'id'))
                                            ->required()
                                            ->preload()
                                            ->searchable()
                                            ->placeholder('Выберите категорию')
                                            ->helperText('К какой категории относится программа'),

                                        TextInput::make('target_group')
                                            ->label('Целевая аудитория')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Например: "Руководители среднего звена"')
                                            ->columnSpanFull()
                                            ->helperText('Для кого предназначена эта программа'),

                                        TextInput::make('qualification')
                                            ->label('Выдаваемый документ')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Например: "Удостоверение о повышении квалификации"')
                                            ->columnSpanFull()
                                            ->helperText('Какой документ получат слушатели'),
                                    ]),

                                Section::make('Параметры обучения')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('price')
                                                    ->label('Стоимость (руб)')
                                                    ->required()
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->placeholder('Укажите стоимость')
                                                    ->helperText('Полная стоимость программы'),

                                                TextInput::make('learning_time')
                                                    ->label('Объем (часов)')
                                                    ->required()
                                                    ->numeric()
                                                    ->minValue(1)
                                                    ->placeholder('Укажите количество часов')
                                                    ->helperText('Общий объем программы в академических часах'),

                                                Select::make('form_education')
                                                    ->label('Форма обучения')
                                                    ->options(FormEducation::class)
                                                    ->required()
                                                    ->native(false)
                                                    ->placeholder('Выберите форму')
                                                    ->helperText('Основная форма проведения занятий'),

                                                Toggle::make('is_active')
                                                    ->label('Активна для записи')
                                                    ->inline(false)
                                                    ->default(true)
                                                    ->helperText('Отображать ли программу на сайте'),
                                            ]),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Содержание программы')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                ContentBuilderItem::getItem('content')
                                    ->label('Описание программы')
                                    ->helperText('Создайте подробное описание программы с помощью конструктора')
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Название')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->target_group)
                    ->limit(50),

                BadgeColumn::make('category.title')
                    ->label('Категория')
                    ->sortable()
                    ->searchable()
                    ->color('primary'),

                TextColumn::make('price')
                    ->label('Стоимость')
                    ->sortable()
                    ->money('RUB')
                    ->alignEnd(),

                TextColumn::make('learning_time')
                    ->label('Часов')
                    ->sortable()
                    ->alignCenter(),

                BadgeColumn::make('form_education')
                    ->label('Форма')
                    ->formatStateUsing(fn ($state) => FormEducation::tryFrom($state->value)?->getLabel())
                    ->color(fn ($state) => FormEducation::tryFrom($state->value)?->getColor())
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Активна')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Обновлено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Категория')
                    ->options(AdditionalEducationCategory::where('is_active', true)->pluck('title', 'id'))
                    ->searchable(),

                Tables\Filters\SelectFilter::make('form_education')
                    ->label('Форма обучения')
                    ->options(FormEducation::class),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Только активные')
                    ->placeholder('Все')
                    ->trueLabel('Активные')
                    ->falseLabel('Неактивные'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->tooltip('Редактировать'),

                Tables\Actions\ViewAction::make()
                    ->iconButton()
                    ->tooltip('Просмотреть'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Удалить выбранные')
                        ->modalHeading('Удаление программ')
                        ->modalDescription('Вы уверены, что хотите удалить выбранные программы ДПО?'),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Добавить программу'),
            ])
            ->defaultSort('title');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdditionalEducation::route('/'),
            'create' => Pages\CreateAdditionalEducation::route('/create'),
            'edit' => Pages\EditAdditionalEducation::route('/{record}/edit'),
        ];
    }
}