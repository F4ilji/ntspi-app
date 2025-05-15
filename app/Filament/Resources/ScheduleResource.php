<?php

namespace App\Filament\Resources;

use App\Containers\Schedule\Models\EducationalGroup;
use App\Containers\Schedule\Models\Schedule;
use App\Filament\Resources\ScheduleResource\Pages;
use App\Ship\Enums\Education\FormEducation;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ScheduleResource extends Resource
{
    protected static ?string $navigationGroup = 'Расписание и группы';
    protected static ?string $model = Schedule::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $pluralLabel = 'Расписания';
    protected static ?string $modelLabel = 'расписание';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Основные настройки')
                    ->description('Основная информация о расписании')
                    ->collapsible()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('educational_group_id')
                                    ->label('Учебная группа')
                                    ->options(EducationalGroup::query()->orderBy('title')->pluck('title', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->helperText('Выберите группу для которой создается расписание'),
                            ]),
                    ]),

                Section::make('Файлы расписания')
                    ->description('Загрузите файлы с расписанием')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Repeater::make('file')
                            ->label('')
                            ->addActionLabel('Добавить файл расписания')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Название файла')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Например: "Расписание на весенний семестр 2024"')
                                    ->helperText('Укажите понятное название файла для идентификации'),

                                FileUpload::make('path')
                                    ->label('Файл PDF')
                                    ->required()
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->maxSize(5120) // 5MB
                                    ->disk('public')
                                    ->directory('schedules')
                                    ->downloadable()
                                    ->openable()
                                    ->previewable(false)
                                    ->helperText('Только PDF файлы, макс. размер 5MB')
                                    ->getUploadedFileNameForStorageUsing(
                                        fn (TemporaryUploadedFile $file): string =>
                                        str(Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '-' . Carbon::now()->timestamp) . '.' . $file->getClientOriginalExtension()
                                        )
                                    )
                                    ->afterStateUpdated(function ($set, $state) {
                                        $set('title', pathinfo($state?->getClientOriginalName(), PATHINFO_FILENAME));
                                    })
                                    ->visibility('public'),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Новый файл')
                            ->collapsible()
                            ->cloneable()
                            ->defaultItems(1),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('educationalGroup.title')
                    ->label('Учебная группа')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('file_count')
                    ->label('Файлов')
                    ->getStateUsing(fn ($record) => count($record->file ?? []))
                    ->badge(),

                TextColumn::make('educationalGroup.education_form_id')
                    ->formatStateUsing(fn ($state) => FormEducation::tryFrom($state)->getLabel())
                    ->color(fn ($state) => FormEducation::tryFrom($state)?->getColor())
                    ->badge()
                    ->label('Форма обучения'),

                TextColumn::make('updated_at')
                    ->label('Обновлено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('educational_group_id')
                    ->label('Учебная группа')
                    ->options(EducationalGroup::query()->orderBy('title')->pluck('title', 'id'))
                    ->searchable(),

                Tables\Filters\TernaryFilter::make('is_zaoch')
                    ->label('Форма обучения')
                    ->placeholder('Все')
                    ->trueLabel('Заочная')
                    ->falseLabel('Очная'),
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
                        ->modalHeading('Удаление расписаний')
                        ->modalDescription('Вы уверены, что хотите удалить выбранные расписания? Это действие нельзя отменить.'),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Добавить расписание'),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchedules::route('/'),
            'create' => Pages\CreateSchedule::route('/create'),
            'edit' => Pages\EditSchedule::route('/{record}/edit'),
        ];
    }
}