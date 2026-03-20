<?php

namespace App\Filament\Resources\DepartmentResource\RelationManagers;

use App\Services\App\Cache\DepartmentCacheService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TeachersRelationManager extends RelationManager
{
    protected static string $relationship = 'teachers';
    protected static ?string $inverseRelationship = 'departments_teach';
    protected static ?string $title = 'Преподаватели кафедры';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Информация о преподавателе')
                    ->description('Основные данные о работе преподавателя на кафедре')
                    ->schema([
                        Forms\Components\TextInput::make('teaching_position')
                            ->label('Преподавательская должность')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Например: Профессор')
                            ->helperText('Официальная преподавательская должность'),

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
                            ->regex('/^\+?[0-9\s\-\(\)]{7,}$/')
                            ->validationMessages([
                                'regex' => 'Пожалуйста, введите корректный номер телефона. Допустимые форматы: +7 (XXX) XXX-XX-XX или XXX-XX-XX',
                            ]),

                        Forms\Components\TextInput::make('cabinet')
                            ->label('Кабинет')
                            ->maxLength(10)
                            ->placeholder('Например: 305а')
                            ->helperText('Номер кабинета преподавателя'),
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

                Tables\Columns\TextColumn::make('teaching_position')
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
                                    ->placeholder('Выбрать преподавателя')
                                    ->columnSpanFull()
                                    ->searchable()
                                    ->preload()
                                    ->helperText('Выберите преподавателя')
                                    ->required(),

                                Forms\Components\TextInput::make('teaching_position')
                                    ->label('Преподавательская должность')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Например: Профессор')
                                    ->helperText('Официальная преподавательская должность'),

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
                                    ->regex('/^\+?[0-9\s\-\(\)]{7,}$/')
                                    ->validationMessages([
                                        'regex' => 'Пожалуйста, введите корректный номер телефона. Допустимые форматы: +7 (XXX) XXX-XX-XX или XXX-XX-XX',
                                    ]),

                                Forms\Components\TextInput::make('cabinet')
                                    ->label('Кабинет')
                                    ->maxLength(10)
                                    ->placeholder('Например: 305а')
                                    ->helperText('Номер кабинета преподавателя'),
                            ])
                            ->columns(1),
                    ])
                    ->modalSubmitActionLabel('Добавить')
                    ->before(function () {
                        app(DepartmentCacheService::class)->clearAllCacheByModel();
                    })
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
                    ->modalDescription('Вы уверены, что хотите убрать этого преподавателя с кафедры?'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make()
                        ->label('Убрать выбранных')
                        ->modalHeading('Удаление связей')
                        ->modalSubmitActionLabel('Убрать')
                        ->modalDescription('Вы уверены, что хотите убрать выбранных преподавателей с кафедры?'),
                ]),
            ])
            ->emptyStateActions([
                AttachAction::make()
                    ->label('Добавить преподавателя'),
            ])
            ->defaultSort('sort')
            ->reorderable('sort')
            ->deferLoading();
    }

    protected function canReorder(): bool
    {
        return true;
    }
}