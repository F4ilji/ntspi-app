<?php

namespace App\Filament\Resources;

use App\Containers\AdditionalEducation\Models\DirectionAdditionalEducation;
use App\Filament\Resources\DirectionAdditionalEducationResource\Pages;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class DirectionAdditionalEducationResource extends Resource
{
    protected static ?string $model = DirectionAdditionalEducation::class;

    protected static ?string $navigationGroup = 'Образование';

    public static ?string $label = 'Направление';
    protected static ?string $pluralLabel = 'Направления дополнительного образования';
    protected static ?string $navigationParentItem = 'Дополнительное Образование';
    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-up';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Основная информация')
                    ->description('Заполните данные о направлении дополнительного образования')
                    ->collapsible()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('title')
                                    ->label('Название направления')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Например: "Информационные технологии"')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, ?string $state, Forms\Set $set) {
                                        $set('slug', Str::slug($state));
                                    })
                                    ->helperText('Укажите понятное название направления'),

                                TextInput::make('slug')
                                    ->label('URL-идентификатор')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->helperText('Человеко-понятный URL для направления'),
                            ]),

                        Toggle::make('is_active')
                            ->label('Активное направление')
                            ->inline(false)
                            ->default(true)
                            ->helperText('Отображать ли направление на сайте')
                            ->columnSpanFull(),
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
                    ->limit(50),

                IconColumn::make('is_active')
                    ->label('Активно')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Дата создания')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Обновлено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
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
                        ->modalHeading('Удаление направлений')
                        ->modalDescription('Вы уверены, что хотите удалить выбранные направления ДПО?'),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Добавить направление'),
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
            'index' => Pages\ListDirectionAdditionalEducation::route('/'),
            'create' => Pages\CreateDirectionAdditionalEducation::route('/create'),
            'edit' => Pages\EditDirectionAdditionalEducation::route('/{record}/edit'),
        ];
    }
}