<?php

namespace App\Filament\Resources\FacultyResource\RelationManagers;

use App\Services\App\Cache\FacultyCacheService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WorkersRelationManager extends RelationManager
{
    protected static string $relationship = 'workers';
    protected static ?string $title = 'Сотрудники факультета';
    protected static ?string $modelLabel = 'сотрудник';
    protected static ?string $pluralModelLabel = 'сотрудники';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('position')
                            ->label('Должность')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Например: Декан факультета')
                            ->helperText('Укажите официальную должность'),

                        Forms\Components\TextInput::make('service_email')
                            ->label('Рабочая почта')
                            ->email()
                            ->maxLength(255)
                            ->placeholder('example@university.ru')
                            ->helperText('Корпоративная электронная почта'),

                        Forms\Components\TextInput::make('service_phone')
                            ->label('Рабочий телефон')
                            ->maxLength(20)
                            ->placeholder('+7 (XXX) XXX-XX-XX')
                            ->helperText('Номер рабочего телефона с кодом'),

                        Forms\Components\TextInput::make('cabinet')
                            ->label('Кабинет')
                            ->maxLength(10)
                            ->placeholder('Например: 305а')
                            ->helperText('Номер кабинета для приема'),

                    ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->defaultSort('sort')
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
                    ->limit(30),

                Tables\Columns\TextColumn::make('service_email')
                    ->label('Почта')
                    ->searchable()
                    ->icon('heroicon-o-envelope'),

                Tables\Columns\TextColumn::make('service_phone')
                    ->label('Телефон')
                    ->searchable()
                    ->icon('heroicon-o-phone'),

                Tables\Columns\TextColumn::make('cabinet')
                    ->label('Кабинет')
                    ->searchable()
                    ->icon('heroicon-o-home-modern'),
//                Tables\Columns\TextColumn::make('sort')
//                    ->label('Сортировка')
//                    ->toggleable(isToggledHiddenByDefault: false),

            ])
            ->headerActions([
                AttachAction::make()
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->label('Сотрудник')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Выберите сотрудника из списка'),

                        Forms\Components\TextInput::make('position')
                            ->label('Должность на факультете')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Например: Старший преподаватель')
                            ->helperText('Укажите должность на этом факультете'),

                        Forms\Components\Grid::make(1)
                            ->schema([
                                Forms\Components\TextInput::make('service_email')
                                    ->label('Рабочая почта')
                                    ->email()
                                    ->maxLength(255)
                                    ->placeholder('example@university.ru')
                                    ->helperText('Корпоративная электронная почта'),

                                Forms\Components\TextInput::make('service_phone')
                                    ->label('Рабочий телефон')
                                    ->maxLength(20)
                                    ->placeholder('+7 (XXX) XXX-XX-XX')
                                    ->helperText('Номер рабочего телефона с кодом'),

                                Forms\Components\TextInput::make('cabinet')
                                    ->label('Кабинет')
                                    ->maxLength(10)
                                    ->placeholder('Например: 305а')
                                    ->helperText('Номер кабинета для приема'),
                            ])
                    ])
                    ->modalHeading('Добавить сотрудника')
                    ->modalSubmitActionLabel('Добавить')
                    ->modalButton('Добавить')
                    ->before(function () {
                        app(FacultyCacheService::class)->clearAllCacheByModel();
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->tooltip('Редактировать')
                    ->before(function () {
                        app(FacultyCacheService::class)->clearAllCacheByModel();
                    }),
                Tables\Actions\DetachAction::make()
                    ->iconButton()
                    ->tooltip('Открепить')
                    ->modalHeading('Открепить сотрудника')
                    ->modalDescription('Вы уверены, что хотите открепить этого сотрудника от факультета?')
                    ->modalSubmitActionLabel('Открепить')
                    ->before(function () {
                        app(FacultyCacheService::class)->clearAllCacheByModel();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make()
                        ->label('Открепить выбранных')
                        ->modalHeading('Открепить сотрудников')
                        ->modalDescription('Вы уверены, что хотите открепить выбранных сотрудников от факультета?')
                        ->modalSubmitActionLabel('Открепить')
                        ->before(function () {
                            app(FacultyCacheService::class)->clearAllCacheByModel();
                        }),
                ]),
            ])
            ->emptyStateHeading('Нет сотрудников')
            ->emptyStateDescription('Добавьте сотрудников, используя кнопку выше')
            ->emptyStateIcon('heroicon-o-user-group');
    }

    protected function canReorder(): bool
    {
        return true;
    }
}