<?php

namespace App\Filament\Resources\EducationalProgramResource\RelationManagers;

use App\Containers\Education\Models\AdmissionCampaign;
use App\Ship\Enums\Education\BudgetEducation;
use App\Ship\Enums\Education\FormEducation;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AdmissionPlansRelationManager extends RelationManager
{
    protected static string $relationship = 'admission_plans';
    protected static ?string $title = 'Планы приема';
    protected static ?string $modelLabel = 'план приема';
    protected static ?string $pluralModelLabel = 'планы приема';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('admission_campaigns_id')
                    ->label('Приемная кампания')
                    ->required()
                    ->columnSpanFull()

                    ->options(
                        AdmissionCampaign::query()
                            ->orderBy('name')
                            ->pluck('name', 'id')
                    )
                    ->searchable()
                    ->preload()
                    ->placeholder('Выберите приемную кампанию')
                    ->helperText('Выберите связанную приемную кампанию'),

                Section::make('План приема')
                    ->description('Настройка вступительных испытаний и условий поступления')
                    ->collapsible()
                    ->schema([
                        self::getExamsRepeater(),
                        self::getContestsRepeater(),
                    ]),
            ]);
    }

    protected static function getExamsRepeater(): Repeater
    {
        return Repeater::make('exams')
            ->label('Вступительные испытания')
            ->schema([
                TextInput::make('title')
                    ->label('Название предмета')
                    ->required()
                    ->maxLength(100)
                    ->placeholder('Например: Математика')
                    ->helperText('Название вступительного испытания'),

                Select::make('type_exam')
                    ->label('Тип испытания')
                    ->required()
                    ->options([
                        'ege' => 'ЕГЭ',
                        'internal_test' => 'Внутреннее испытание',
                    ])
                    ->native(false)
                    ->placeholder('Выберите тип')
                    ->helperText('Тип вступительного испытания'),

                TextInput::make('min_score')
                    ->label('Минимальный балл')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->placeholder('Укажите минимальный балл')
                    ->helperText('Минимальный проходной балл'),
            ])
            ->columns(3)
            ->maxItems(10)
            ->collapsible()
            ->collapsed()
            ->addActionLabel('Добавить испытание')
            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Новое испытание')
            ->helperText('Добавьте все необходимые вступительные испытания');
    }

    protected static function getContestsRepeater(): Repeater
    {
        return Repeater::make('contests')
            ->label('Условия поступления')
            ->schema([
                Select::make('form_education')
                    ->label('Форма обучения')
                    ->options(FormEducation::class)
                    ->required()
                    ->native(false)
                    ->placeholder('Выберите форму')
                    ->columnSpanFull()
                    ->helperText('Форма обучения для данной группы'),

                Repeater::make('places')
                    ->label('Места')
                    ->schema([
                        Select::make('form_budget')
                            ->label('Форма финансирования')
                            ->options(BudgetEducation::class)
                            ->required()
                            ->native(false)
                            ->placeholder('Выберите тип')
                            ->helperText('Бюджетные или платные места'),

                        TextInput::make('count')
                            ->label('Количество мест')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->placeholder('Укажите количество')
                            ->helperText('Количество доступных мест'),
                    ])
                    ->columnSpanFull()
                    ->maxItems(2)
                    ->addActionLabel('Добавить тип мест')
            ])
            ->columns(2)
            ->maxItems(3)
            ->collapsible()
            ->collapsed()
            ->addActionLabel('Добавить группу')
            ->helperText('Добавьте группы с условиями поступления');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('admissionCampaign.name')
                    ->label('Приемная кампания')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('exams_count')
                    ->label('Испытаний')
                    ->getStateUsing(fn ($record) => count($record->exams ?? []))
                    ->badge(),

                TextColumn::make('contests_count')
                    ->label('Групп')
                    ->getStateUsing(fn ($record) => count($record->contests ?? []))
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Добавить план')
                    ->modalHeading('Создание плана приема'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->tooltip('Редактировать'),

                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Удалить'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Удалить выбранные')
                        ->modalHeading('Удаление планов приема')
                        ->modalDescription('Вы уверены, что хотите удалить выбранные планы?'),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Добавить план приема'),
            ])
            ->defaultSort('admissionCampaign.name');
    }
}