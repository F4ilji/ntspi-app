<?php

namespace App\Filament\Resources;

use App\Containers\Education\Models\DirectionStudy;
use App\Filament\Resources\DirectionStudyResource\Pages;
use App\Ship\Enums\Education\LevelEducational;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class DirectionStudyResource extends Resource
{
    protected static ?string $model = DirectionStudy::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $pluralLabel = 'Направления подготовки';
    protected static ?string $modelLabel = 'Направление подготовки';

    protected static ?string $navigationGroup = 'Образование';
    protected static ?string $navigationParentItem = 'Приемная-компания';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Основная информация')
                    ->description('Заполните основные данные о направлении подготовки')
                    ->collapsible()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Название направления')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Например: Информатика и вычислительная техника')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, ?string $state, Forms\Set $set) {
                                        $set('slug', Str::slug($state));
                                    })
                                    ->helperText('Полное название направления подготовки'),

                                TextInput::make('slug')
                                    ->label('URL-идентификатор')
                                    ->required()
                                    ->readOnly()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->helperText('Человеко-понятный URL для направления'),

                                TextInput::make('code')
                                    ->label('Код направления')
                                    ->required()
                                    ->maxLength(50)
                                    ->placeholder('Например: 09.03.01')
                                    ->helperText('Код направления по ФГОС'),

                                Select::make('lvl_edu')
                                    ->label('Уровень образования')
                                    ->options(LevelEducational::class)
                                    ->required()
                                    ->native(false)
                                    ->placeholder('Выберите уровень')
                                    ->helperText('Выберите уровень образовательной программы'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Код')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->name),

                BadgeColumn::make('lvl_edu')
                    ->label('Уровень')
                    ->formatStateUsing(fn ($state) => LevelEducational::tryFrom($state->value)?->getLabel())
                    ->color(fn ($state) => LevelEducational::tryFrom($state->value)?->getColor())
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Добавлено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('lvl_edu')
                    ->label('Уровень образования')
                    ->options(LevelEducational::class),
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
                        ->modalDescription('Вы уверены, что хотите удалить выбранные направления подготовки?'),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Добавить направление'),
            ])
            ->defaultSort('code');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDirectionStudies::route('/'),
            'create' => Pages\CreateDirectionStudy::route('/create'),
            'edit' => Pages\EditDirectionStudy::route('/{record}/edit'),
        ];
    }
}