<?php

namespace App\Filament\Resources;

use App\Containers\InstituteStructure\Models\Faculty;
use App\Filament\Components\Forms\ItemForm\Pages\ContentBuilderItem;
use App\Filament\Resources\FacultyResource\Pages;
use App\Filament\Resources\FacultyResource\RelationManagers\DepartmentsRelationManager;
use App\Filament\Resources\FacultyResource\RelationManagers\WorkersRelationManager;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
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

class FacultyResource extends Resource
{
    protected static ?string $model = Faculty::class;
    protected static ?string $navigationGroup = 'Структура института';
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $pluralLabel = 'Факультеты';
    protected static ?string $modelLabel = 'факультет';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Настройки факультета')
                    ->persistTabInQueryString()
                    ->columnSpanFull()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Основные данные')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make('Идентификация')
                                    ->description('Основная информация о факультете')
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Полное название')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Например: Факультет информационных технологий')
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (string $operation, ?string $state, Forms\Set $set) {
                                                $set('slug', Str::slug($state));
                                                if ($operation !== 'edit') {
                                                    $set('seo.title', $state);
                                                }
                                            })
                                            ->helperText('Официальное название факультета'),

                                        TextInput::make('slug')
                                            ->label('URL-адрес')
                                            ->unique(ignoreRecord: true)
                                            ->required()
                                            ->readOnly()
                                            ->helperText('Формируется автоматически из названия')
                                            ->prefix(fn () => route('client.faculty.index') . '/')
                                            ->suffixAction(
                                                Action::make('copy')
                                                    ->icon('heroicon-s-clipboard-document-check')
                                                    ->action(function ($livewire, $state) {
                                                        $livewire->js(
                                                            'window.navigator.clipboard.writeText("'. route('client.faculty.index') . '/' . $state.'");
                    $tooltip("'.__('Copied to clipboard').'", { timeout: 1500 });'
                                                        );
                                                    })),

                                        TextInput::make('abbreviation')
                                            ->label('Аббревиатура')
                                            ->required()
                                            ->maxLength(10)
                                            ->placeholder('Например: ФИТ')
                                            ->helperText('Короткое обозначение факультета'),

                                        Toggle::make('is_active')
                                            ->label('Активный факультет')
                                            ->inline(false)
                                            ->default(true)
                                            ->helperText('Отображать ли факультет на сайте'),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Контент')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                ContentBuilderItem::getItem('content')
                            ]),
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
                    ->description(fn ($record) => $record->abbreviation),

                IconColumn::make('is_active')
                    ->label('Статус')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

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
                        ->modalHeading('Удаление факультетов')
                        ->modalDescription('Вы уверены, что хотите удалить выбранные факультеты?'),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Добавить факультет'),
            ])
            ->defaultSort('title');
    }

    public static function getRelations(): array
    {
        return [
            WorkersRelationManager::class,
            DepartmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFaculties::route('/'),
            'create' => Pages\CreateFaculty::route('/create'),
            'edit' => Pages\EditFaculty::route('/{record}/edit'),
        ];
    }
}