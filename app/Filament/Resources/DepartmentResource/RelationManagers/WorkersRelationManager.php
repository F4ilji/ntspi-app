<?php

namespace App\Filament\Resources\DepartmentResource\RelationManagers;

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
    protected static ?string $inverseRelationship = 'departments_work';
    protected static ?string $title = 'Сотрудники кафедры';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Информация о должности')
                    ->description('Основные данные о работе сотрудника на кафедре')
                    ->schema([
                        Forms\Components\TextInput::make('position')
                            ->label('Должность')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Например: Заведующий кафедрой')
                            ->helperText('Официальная должность сотрудника на кафедре'),

                        Forms\Components\TextInput::make('service_email')
                            ->label('Служебная почта')
                            ->email()
                            ->maxLength(255)
                            ->placeholder('example@university.edu')
                            ->helperText('Корпоративная электронная почта'),

                        Forms\Components\TextInput::make('service_phone')
                            ->label('Служебный телефон')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('+7 (XXX) XXX-XX-XX')
                            ->helperText('Формат: +7 (XXX) XXX-XX-XX')
                            ->regex('/^\+?[0-9\s\-\(\)]{7,}$/') // Разрешаем +, цифры, пробелы, дефисы, скобки
                            ->validationMessages([
                                'regex' => 'Пожалуйста, введите корректный номер телефона. Допустимые форматы: +7 (XXX) XXX-XX-XX или XXX-XX-XX',
                            ]),

                        Forms\Components\TextInput::make('cabinet')
                            ->label('Кабинет')
                            ->maxLength(10)
                            ->placeholder('Например: 305а')
                            ->helperText('Номер кабинета сотрудника'),
                    ])
                    ->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('ФИО')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('position')
                    ->label('Должность')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('service_email')
                    ->label('Почта')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('cabinet')
                    ->label('Кабинет')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(fn (Builder $query) => $query->has('userDetail'))
                    ->form(fn (AttachAction $action): array => [
                        Forms\Components\Section::make('')
                            ->schema([
                                $action->getRecordSelect()
                                    ->placeholder('Выбрать сотрудника')
                                    ->columnSpanFull()
                                    ->searchable()
                                    ->preload()
                                    ->helperText('Выберите сотрудника')
                                    ->required(),

                                Forms\Components\TextInput::make('position')
                                    ->label('Должность')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Например: Заведующий кафедрой')
                                    ->helperText('Официальная должность сотрудника на кафедре'),

                                Forms\Components\TextInput::make('service_email')
                                    ->label('Служебная почта')
                                    ->email()
                                    ->maxLength(255)
                                    ->placeholder('example@university.edu')
                                    ->helperText('Корпоративная электронная почта'),

                                Forms\Components\TextInput::make('service_phone')
                                    ->label('Служебный телефон')
                                    ->tel()
                                    ->maxLength(20)
                                    ->placeholder('+7 (XXX) XXX-XX-XX')
                                    ->helperText('Формат: +7 (XXX) XXX-XX-XX')
                                    ->regex('/^\+?[0-9\s\-\(\)]{7,}$/') // Разрешаем +, цифры, пробелы, дефисы, скобки
                                    ->validationMessages([
                                        'regex' => 'Пожалуйста, введите корректный номер телефона. Допустимые форматы: +7 (XXX) XXX-XX-XX или XXX-XX-XX',
                                    ]),

                                Forms\Components\TextInput::make('cabinet')
                                    ->label('Кабинет')
                                    ->maxLength(10)
                                    ->placeholder('Например: 305а')
                                    ->helperText('Номер кабинета сотрудника'),
                            ])
                            ->columns(2),
                    ])
                    ->modalSubmitActionLabel('Добавить')
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->tooltip('Редактировать'),

                Tables\Actions\DetachAction::make()
                    ->iconButton()
                    ->tooltip('Убрать с кафедры')
                    ->modalHeading('Удаление связи')
                    ->modalSubmitActionLabel('Убрать')
                    ->modalDescription('Вы уверены, что хотите убрать этого сотрудника с кафедры?'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make()
                        ->label('Убрать выбранных')
                        ->modalHeading('Удаление связей')
                        ->modalSubmitActionLabel('Убрать')
                        ->modalDescription('Вы уверены, что хотите убрать выбранных сотрудников с кафедры?'),
                ]),
            ])
            ->emptyStateActions([
                AttachAction::make()
                    ->label('Добавить сотрудника'),
            ])
            ->defaultSort('name')
            ->deferLoading();
    }
}