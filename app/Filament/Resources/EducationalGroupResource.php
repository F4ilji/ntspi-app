<?php

namespace App\Filament\Resources;

use App\Enums\FormEducation;
use App\Filament\Resources\EducationalGroupResource\Pages;
use App\Models\EducationalGroup;
use App\Models\Faculty;
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
use Illuminate\Database\Eloquent\Builder;

class EducationalGroupResource extends Resource
{
    protected static ?string $navigationGroup = 'Расписание и группы';
    protected static ?string $model = EducationalGroup::class;
    protected static ?string $pluralLabel = 'Учебные группы';
    protected static ?string $modelLabel = 'учебная группа';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Основная информация')
                    ->description('Заполните основные данные о группе')
                    ->collapsible()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('title')
                                    ->label('Название группы')
                                    ->required()
                                    ->maxLength(50)
                                    ->placeholder('Например: ИВТ-21-1')
                                    ->helperText('Введите краткое название группы в принятом формате'),

                                Select::make('faculty_id')
                                    ->label('Факультет')
                                    ->required()
                                    ->options(Faculty::query()->orderBy('title')->pluck('title', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Выберите факультет')
                                    ->helperText('Выберите факультет, к которому относится группа'),

                                Select::make('education_form_id')
                                    ->label('Форма обучения')
                                    ->required()
                                    ->options(FormEducation::class)
                                    ->native(false)
                                    ->placeholder('Выберите форму обучения')
                                    ->helperText('Выберите форму обучения для группы'),
                            ]),
                    ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('faculty');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('title')
                    ->label('Название группы')
                    ->sortable()
                    ->searchable()
                    ->description(fn ($record) => $record->faculty->title ?? ''),

                TextColumn::make('education_form_id')
                    ->label('Форма обучения')
                    ->badge()
                    ->formatStateUsing(fn ($state) => FormEducation::tryFrom($state)?->getLabel())
                    ->color(fn ($state) => FormEducation::tryFrom($state)?->getColor())
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Дата создания')
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->toggleable(),
            ])

            ->filters([
                Tables\Filters\SelectFilter::make('faculty_id')
                    ->label('Факультет')
                    ->options(Faculty::query()->orderBy('title')->pluck('title', 'id'))
                    ->searchable(),

                Tables\Filters\SelectFilter::make('education_form_id')
                    ->label('Форма обучения')
                    ->options(FormEducation::class),
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
                        ->modalHeading('Удаление групп')
                        ->modalDescription('Вы уверены, что хотите удалить выбранные учебные группы?'),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Добавить группу'),
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
            'index' => Pages\ListEducationalGroups::route('/'),
            'create' => Pages\CreateEducationalGroup::route('/create'),
            'edit' => Pages\EditEducationalGroup::route('/{record}/edit'),
        ];
    }
}