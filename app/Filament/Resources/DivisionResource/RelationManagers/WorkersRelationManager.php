<?php

namespace App\Filament\Resources\DivisionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WorkersRelationManager extends RelationManager
{
    protected static string $relationship = 'workers';
    protected static ?string $title = 'Сотрудники подразделения';
    protected static ?string $inverseRelationship = 'divisions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Служебная информация')
                    ->description('Данные о сотруднике в рамках подразделения')
                    ->schema([
                        Forms\Components\TextInput::make('administrativePosition')
                            ->label('Административная должность')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Например: Руководитель отдела')
                            ->helperText('Официальная должность в подразделении'),

                        Forms\Components\TextInput::make('service_email')
                            ->label('Служебная почта')
                            ->email()
                            ->maxLength(255)
                            ->placeholder('example@university.edu')
                            ->helperText('Корпоративная электронная почта в подразделении'),

                        Forms\Components\TextInput::make('service_phone')
                            ->label('Служебный телефон')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('+7 (XXX) XXX-XX-XX')
                            ->helperText('Формат: +7 (XXX) XXX-XX-XX')
                            ->regex('/^\+?[0-9\s\-\(\)]{7,}$/')
                            ->validationMessages([
                                'regex' => 'Пожалуйста, введите корректный номер телефона',
                            ]),

                        Forms\Components\TextInput::make('cabinet')
                            ->label('Кабинет')
                            ->maxLength(10)
                            ->placeholder('Например: 305а')
                            ->helperText('Номер кабинета в подразделении'),
                    ])
                    ->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                Tables\Columns\TextColumn::make('sort')
                    ->label('№')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('name')
                    ->label('ФИО')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn ($record) => $record->service_email),

                Tables\Columns\TextColumn::make('administrativePosition')
                    ->label('Должность')
                    ->searchable()
                    ->wrap()
                    ->description(fn ($record) => $record->cabinet),

                Tables\Columns\TextColumn::make('service_phone')
                    ->label('Телефон')
                    ->searchable()
                    ->toggleable(),
            ])
            ->filters([
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Добавить сотрудника')
                    ->modalHeading('Добавление сотрудника')
                    ->modalSubmitActionLabel('Добавить')
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(fn (Builder $query) => $query->has('userDetail'))
                    ->form(fn (AttachAction $action): array => [
                        Forms\Components\Section::make()
                            ->schema([
                                $action->getRecordSelect()
                                    ->label('Сотрудник')
                                    ->placeholder('Выберите сотрудника')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('administrativePosition')
                                    ->label('Административная должность')
                                    ->required()
                                    ->columnSpanFull()
                                    ->maxLength(255)
                                    ->placeholder('Например: Руководитель отдела')
                                    ->helperText('Официальная должность в подразделении'),

                                Forms\Components\TextInput::make('service_email')
                                    ->label('Служебная почта')
                                    ->email()
                                    ->columnSpanFull()
                                    ->maxLength(255)
                                    ->placeholder('example@university.edu')
                                    ->helperText('Корпоративная электронная почта в подразделении'),

                                Forms\Components\TextInput::make('service_phone')
                                    ->label('Служебный телефон')
                                    ->tel()
                                    ->columnSpanFull()
                                    ->maxLength(20)
                                    ->placeholder('+7 (XXX) XXX-XX-XX')
                                    ->helperText('Формат: +7 (XXX) XXX-XX-XX')
                                    ->regex('/^\+?[0-9\s\-\(\)]{7,}$/')
                                    ->validationMessages([
                                        'regex' => 'Пожалуйста, введите корректный номер телефона',
                                    ]),

                                Forms\Components\TextInput::make('cabinet')
                                    ->label('Кабинет')
                                    ->columnSpanFull()
                                    ->maxLength(10)
                                    ->placeholder('Например: 305а')
                                    ->helperText('Номер кабинета в подразделении'),
                            ])
                            ->columns(2),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->tooltip('Редактировать данные сотрудника'),

                Tables\Actions\DetachAction::make()
                    ->iconButton()
                    ->tooltip('Убрать из подразделения')
                    ->modalHeading('Подтверждение удаления')
                    ->modalSubmitActionLabel('Убрать')
                    ->modalDescription('Вы уверены, что хотите убрать этого сотрудника из подразделения?'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make()
                        ->label('Убрать выбранных')
                        ->modalHeading('Подтверждение удаления')
                        ->modalSubmitActionLabel('Убрать')
                        ->modalDescription('Вы уверены, что хотите убрать выбранных сотрудников из подразделения?'),
                ]),
            ])
            ->emptyStateActions([
                AttachAction::make()
                    ->label('Добавить сотрудника'),
            ])
            ->persistFiltersInSession()
            ->paginated([10, 25, 50, 100])
            ->striped();
    }
}