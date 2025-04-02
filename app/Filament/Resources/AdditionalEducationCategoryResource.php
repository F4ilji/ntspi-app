<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdditionalEducationCategoryResource\Pages;
use App\Models\AdditionalEducationCategory;
use App\Models\DirectionAdditionalEducation;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class AdditionalEducationCategoryResource extends Resource
{
    protected static ?string $model = AdditionalEducationCategory::class;

    protected static ?string $navigationGroup = 'Образование';

    public static ?string $label = 'Категория';
    protected static ?string $pluralLabel = 'Категории дополнительного образования';
    protected static ?string $navigationParentItem = 'Дополнительное Образование';

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Основная информация')
                    ->description('Заполните данные о категории программ ДПО')
                    ->collapsible()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('title')
                                    ->label('Название категории')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Например: "Профессиональная переподготовка"')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, ?string $state, Forms\Set $set) {
                                        $set('slug', Str::slug($state));
                                    })
                                    ->helperText('Укажите понятное название категории'),

                                TextInput::make('slug')
                                    ->label('URL-идентификатор')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->helperText('Человеко-понятный URL для категории'),

                                Select::make('dir_addit_educat_id')
                                    ->label('Направление ДПО')
                                    ->options(
                                        DirectionAdditionalEducation::where('is_active', true)
                                            ->orderBy('title')
                                            ->pluck('title', 'id')
                                    )
                                    ->required()
                                    ->preload()
                                    ->searchable()
                                    ->placeholder('Выберите направление')
                                    ->helperText('К какому направлению относится категория'),
                            ]),

                        Toggle::make('is_active')
                            ->label('Активная категория')
                            ->inline(false)
                            ->default(true)
                            ->helperText('Отображать ли категорию на сайте')
                            ->columnSpanFull(),
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
                    ->description(fn ($record) => $record->direction->title ?? '')
                    ->limit(50),

                BadgeColumn::make('direction.title')
                    ->label('Направление')
                    ->sortable()
                    ->searchable()
                    ->color('primary'),

                IconColumn::make('is_active')
                    ->label('Активна')
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
                Tables\Filters\SelectFilter::make('dir_addit_educat_id')
                    ->label('Направление ДПО')
                    ->options(
                        DirectionAdditionalEducation::where('is_active', true)
                            ->orderBy('title')
                            ->pluck('title', 'id')
                    )
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
                        ->modalHeading('Удаление категорий')
                        ->modalDescription('Вы уверены, что хотите удалить выбранные категории ДПО?'),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Добавить категорию'),
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
            'index' => Pages\ListAdditionalEducationCategories::route('/'),
            'create' => Pages\CreateAdditionalEducationCategory::route('/create'),
            'edit' => Pages\EditAdditionalEducationCategory::route('/{record}/edit'),
        ];
    }
}