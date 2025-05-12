<?php

namespace App\Filament\Resources;

use App\Containers\InstituteStructure\Models\Department;
use App\Containers\InstituteStructure\Models\Faculty;
use App\Filament\Components\Forms\ItemForm\Pages\ContentBuilderItem;
use App\Filament\Resources\DepartmentResource\Pages;
use App\Filament\Resources\DepartmentResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;
    protected static ?string $navigationGroup = 'Структура института';
    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $pluralLabel = 'Кафедры';
    protected static ?string $modelLabel = 'кафедра';
    protected static ?string $navigationParentItem = 'Факультеты';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Настройки кафедры')
                    ->persistTabInQueryString()
                    ->columnSpanFull()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Основные данные')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make('Идентификация')
                                    ->description('Основная информация о кафедре')
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Полное название')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Например: Кафедра программной инженерии')
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (string $operation, ?string $state, Forms\Set $set) {
                                                $set('slug', Str::slug($state));
                                            })
                                            ->helperText('Официальное название кафедры'),

                                        TextInput::make('slug')
                                            ->label('URL-идентификатор')
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(ignoreRecord: true)
                                            ->helperText('Человеко-понятный URL для страницы кафедры'),

                                        Select::make('faculty_id')
                                            ->label('Факультет')
                                            ->options(Faculty::query()->orderBy('title')->pluck('title', 'id'))
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->native(false)
                                            ->helperText('К какому факультету относится кафедра'),

                                        Toggle::make('is_active')
                                            ->label('Активная кафедра')
                                            ->inline(false)
                                            ->default(true)
                                            ->helperText('Отображать ли кафедру на сайте'),
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
                    ->description(fn ($record) => $record->faculty->abbreviation ?? ''),

                TextColumn::make('faculty.title')
                    ->label('Факультет')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

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
                Tables\Filters\SelectFilter::make('faculty_id')
                    ->label('Факультет')
                    ->options(Faculty::query()->orderBy('title')->pluck('title', 'id'))
                    ->searchable(),

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
                        ->modalHeading('Удаление кафедр')
                        ->modalDescription('Вы уверены, что хотите удалить выбранные кафедры?'),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Добавить кафедру'),
            ])
            ->defaultSort('title');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\WorkersRelationManager::class,
            RelationManagers\TeachersRelationManager::class,
            RelationManagers\ProgramsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDepartments::route('/'),
            'create' => Pages\CreateDepartment::route('/create'),
            'edit' => Pages\EditDepartment::route('/{record}/edit'),
        ];
    }
}