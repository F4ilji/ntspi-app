<?php

namespace App\Filament\Resources\AcademicJournalResource\RelationManagers;

use App\Models\AcademicJournal;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JournalsRelationManager extends RelationManager
{
    protected static string $relationship = 'journals';

    protected static ?string $modelLabel = 'выпуск';
    protected static ?string $pluralModelLabel = 'выпуски';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->label('Название выпуска')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Введите название выпуска журнала')
                    ->helperText('Например: "Том 15, №3 (2023)" или специальное название выпуска'),

                FileUpload::make('path_file')
                    ->label('Файл выпуска')
                    ->required()
                    ->acceptedFileTypes([
                        'application/pdf' => 'PDF',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'DOCX',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'XLSX',
                        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'PPTX',
                        'application/zip' => 'ZIP',
                    ])
                    ->maxSize(512000)
                    ->disk('public')
                    ->directory('journals/files')
                    ->downloadable()
                    ->visibility('public')
                    ->helperText('Максимальный размер файла: 512MB. Допустимые форматы: PDF, DOCX, XLSX, PPTX, ZIP')
                    ->openable()
                    ->previewable(false),


                TextInput::make('year_publication')
                    ->label('Год публикации')
                    ->required()
                    ->numeric()
                    ->minValue(1900)
                    ->maxValue(now()->year + 1)
                    ->placeholder('Укажите год выпуска')
                    ->helperText('Год должен быть в диапазоне от 1900 до '.(now()->year + 1)),

                Toggle::make('is_active')
                    ->label('Активный выпуск')
                    ->default(true)
                    ->inline(false)
                    ->helperText('Активные выпуски отображаются на сайте'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultGroup('year_publication')
            ->reorderable('sort')
            ->defaultSort('sort')
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Название выпуска')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->year_publication),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Статус')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата добавления')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('year_publication')
                    ->label('Год выпуска')
                    ->options(
                        fn () => $this->getOwnerRecord()
                            ->journals()
                            ->select('year_publication')
                            ->distinct()
                            ->orderBy('year_publication', 'desc')
                            ->pluck('year_publication', 'year_publication')
                            ->toArray()
                    ),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Только активные')
                    ->trueLabel('Активные')
                    ->falseLabel('Неактивные')
                    ->queries(
                        true: fn (Builder $query) => $query->where('is_active', true),
                        false: fn (Builder $query) => $query->where('is_active', false),
                    ),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Добавить выпуск')
                    ->modalHeading('Добавление нового выпуска'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->tooltip('Редактировать'),

                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Удалить')
                    ->modalHeading('Удаление выпуска')
                    ->modalDescription('Вы уверены, что хотите удалить этот выпуск?'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Удалить выбранные')
                        ->modalHeading('Удаление выпусков')
                        ->modalDescription('Вы уверены, что хотите удалить выбранные выпуски?'),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Добавить выпуск'),
            ])
            ->groups([
                Tables\Grouping\Group::make('year_publication')
                    ->label('Год публикации')
                    ->collapsible(),
            ]);
    }
}