<?php

namespace App\Filament\Resources;

use App\Enums\AdmissionCampaignStatus;
use App\Enums\LevelEducational;
use App\Filament\Resources\AdmissionCampaignResource\Pages;
use App\Models\AdmissionCampaign;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AdmissionCampaignResource extends Resource
{
    protected static ?string $model = AdmissionCampaign::class;
    protected static ?string $navigationGroup = 'Образование';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $pluralLabel = 'Приемная-компания';
    protected static ?string $modelLabel = 'Приемная кампания';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Основные настройки')
                    ->description('Общая информация о приемной кампании')
                    ->collapsible()
                    ->schema([
                        TextInput::make('name')
                            ->label('Название кампании')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Например: "Приемная кампания 2024"')
                            ->columnSpanFull()
                            ->helperText('Укажите понятное название для идентификации кампании'),

                        Grid::make(2)
                            ->schema([
                                Select::make('academic_year')
                                    ->label('Академический год')
                                    ->required()
                                    ->options(self::generateAcademicYears())
                                    ->searchable()
                                    ->placeholder('Выберите учебный год')
                                    ->helperText('Выберите учебный год, к которому относится кампания'),

                                Select::make('status')
                                    ->label('Статус кампании')
                                    ->required()
                                    ->options(AdmissionCampaignStatus::class)
                                    ->native(false)
                                    ->placeholder('Выберите статус')
                                    ->helperText('Определяет видимость и доступность кампании'),
                            ]),
                    ]),

                Section::make('Информация о наборе')
                    ->description('Данные о программах и местах для разных уровней образования')
                    ->collapsible()
                    ->schema([
                        Repeater::make('info')
                            ->label('')
                            ->addActionLabel('Добавить уровень образования')
                            ->schema([
                                Select::make('edu_name')
                                    ->label('Уровень образования')
                                    ->options(LevelEducational::class)
                                    ->required()
                                    ->native(false)
                                    ->placeholder('Выберите уровень образования')
                                    ->helperText('Выберите уровень образовательной программы'),

                                Grid::make(2)
                                    ->schema([
                                        Section::make('Программы')
                                            ->schema([
                                                TextInput::make('total_programs')
                                                    ->label('Количество программ')
                                                    ->required()
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->placeholder('Укажите количество')
                                                    ->helperText('Общее количество программ по набору'),
                                            ]),

                                        Section::make('Распределение мест')
                                            ->schema([
                                                TextInput::make('och_count')
                                                    ->label('Очная форма')
                                                    ->required()
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->placeholder('Укажите количество')
                                                    ->helperText('Количество мест на очной форме'),

                                                TextInput::make('zaoch_count')
                                                    ->label('Заочная форма')
                                                    ->required()
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->placeholder('Укажите количество')
                                                    ->helperText('Количество мест на заочной форме'),

                                                TextInput::make('budget_places')
                                                    ->label('Бюджетные места')
                                                    ->required()
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->placeholder('Укажите количество')
                                                    ->helperText('Количество бюджетных мест'),

                                                TextInput::make('non_budget_places')
                                                    ->label('Платные места')
                                                    ->required()
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->placeholder('Укажите количество')
                                                    ->helperText('Количество платных мест'),
                                            ]),
                                    ]),
                            ])
                            ->itemLabel(fn (array $state): ?string =>
                                LevelEducational::tryFrom($state['edu_name'] ?? '')?->getLabel() ?? 'Новый уровень')
                            ->collapsible()
                            ->cloneable()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Название')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->academic_year),

                BadgeColumn::make('status')
                    ->label('Статус')
                    ->formatStateUsing(fn ($state) => AdmissionCampaignStatus::tryFrom($state)?->getLabel())
                    ->color(fn ($state) => AdmissionCampaignStatus::tryFrom($state)?->getColor())
                    ->sortable(),

                TextColumn::make('info_count')
                    ->label('Программ')
                    ->getStateUsing(fn ($record) => count($record->info ?? []))
                    ->badge(),

                TextColumn::make('updated_at')
                    ->label('Обновлено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options(AdmissionCampaignStatus::class),

                Tables\Filters\SelectFilter::make('academic_year')
                    ->label('Учебный год')
                    ->options(self::generateAcademicYears()),
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
                        ->modalHeading('Удаление приемных кампаний')
                        ->modalDescription('Вы уверены, что хотите удалить выбранные кампании? Это действие нельзя отменить.'),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Создать кампанию'),
            ])
            ->defaultSort('academic_year', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdmissionCampaigns::route('/'),
            'create' => Pages\CreateAdmissionCampaign::route('/create'),
            'edit' => Pages\EditAdmissionCampaign::route('/{record}/edit'),
        ];
    }

    protected static function generateAcademicYears(): array
    {
        $currentYear = (int) date('Y') - 5;
        $yearsAhead = 10;
        $academicYears = [];

        for ($i = 0; $i < $yearsAhead; $i++) {
            $startYear = $currentYear + $i;
            $endYear = $startYear + 1;
            $academicYears["{$startYear}/{$endYear}"] = "{$startYear}/{$endYear}";
        }

        return $academicYears;
    }
}