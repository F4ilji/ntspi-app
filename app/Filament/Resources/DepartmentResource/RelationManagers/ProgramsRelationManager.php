<?php

namespace App\Filament\Resources\DepartmentResource\RelationManagers;

use App\Enums\EducationalProgramStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProgramsRelationManager extends RelationManager
{
    protected static string $relationship = 'programs';
    protected static ?string $title = 'Образовательные программы кафедры';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')
                    ->description('Связь образовательной программы с кафедрой')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Название программы')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Например: Информатика и вычислительная техника')
                            ->helperText('Полное название образовательной программы'),
                    ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Название программы')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->formatStateUsing(fn($state): string => EducationalProgramStatus::tryFrom($state)->getLabel())
                    ->color(fn($state): string => EducationalProgramStatus::tryFrom($state)->getColor())
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус программы')
                    ->options(EducationalProgramStatus::class)
                    ->default(EducationalProgramStatus::PUBLISHED->value),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Добавить программу')
                    ->modalHeading('Добавление программы к кафедре')
                    ->modalSubmitActionLabel('Добавить')
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(fn(Builder $query) => $query->where('status', EducationalProgramStatus::PUBLISHED))
                    ->recordSelect(
                        fn(Forms\Components\Select $select) => $select
                            ->placeholder('Выберите программу')
                            ->label('Образовательная программа')
                            ->helperText('Только опубликованные программы')
                            ->searchable()
                            ->columnSpanFull()
                    )
            ])
            ->actions([

                Tables\Actions\DetachAction::make()
                    ->iconButton()
                    ->tooltip('Открепить программу')
                    ->modalHeading('Открепление программы')
                    ->modalSubmitActionLabel('Открепить')
                    ->modalDescription('Вы уверены, что хотите открепить эту программу от кафедры?'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make()
                        ->label('Открепить выбранные')
                        ->modalHeading('Открепление программ')
                        ->modalSubmitActionLabel('Открепить')
                        ->modalDescription('Вы уверены, что хотите открепить выбранные программы от кафедры?'),
                ]),
            ])
            ->emptyStateActions([
                AttachAction::make()
                    ->label('Добавить программу'),
            ])
            ->defaultSort('name')
            ->deferLoading()
            ->persistFiltersInSession();
    }
}