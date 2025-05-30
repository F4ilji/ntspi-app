<?php

namespace App\Filament\Resources;

use App\Containers\Education\Enums\TypeExam;
use App\Containers\Education\Models\AdmissionCampaign;
use App\Containers\Education\Models\AdmissionPlan;
use App\Containers\Education\Models\EducationalProgram;
use App\Filament\Resources\AdmissionPlanResource\Pages;
use App\Ship\Enums\Education\BudgetEducation;
use App\Ship\Enums\Education\EducationalProgramStatus;
use App\Ship\Enums\Education\FormEducation;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AdmissionPlanResource extends Resource
{
    protected static ?string $model = AdmissionPlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Образование';

    protected static ?string $pluralLabel = 'План приема';

    protected static ?string $navigationParentItem = 'Приемная-компания';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    Select::make('educational_programs_id')
                        ->label('Приемная кампания')
                        ->required()
                        ->columnSpanFull()
                        ->options(EducationalProgram::whereIn('status', [EducationalProgramStatus::PUBLISHED, EducationalProgramStatus::IN_PROGRESS])->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->placeholder('Выберите образовательную программу'),

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
                ]),

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
                    ->columnSpanFull()
                    ->maxLength(100)
                    ->placeholder('Например: Математика')
                    ->helperText('Название вступительного испытания'),
                TextInput::make('priority')
                    ->label('Приоритет предмета')
                    ->required()
                    ->numeric()
                    ->columnSpanFull()
                    ->maxLength(10),

                Repeater::make('types')->schema([
                    Select::make('type')
                        ->label('Тип испытания')
                        ->required()
                        ->options(TypeExam::class)
                        ->native(false)
                        ->placeholder('Выберите тип')
                        ->helperText('Тип вступительного испытания'),

                    TextInput::make('min_ball')
                        ->label('Минимальный балл')
                        ->required()
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->placeholder('Укажите минимальный балл')
                        ->helperText('Минимальный проходной балл'),
                ])
                    ->collapsible()
                    ->collapsed()
                    ->label('Вид встпутиельного испытания')
                    ->addActionLabel('Добавить вид экзамена')
                    ->maxItems(2)
                    ->columnSpanFull(),

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
                Section::make()->schema([
                    Select::make('places.form_budget')
                        ->label('Форма финансирования')
                        ->options(BudgetEducation::class)
                        ->required()
                        ->native(false)
                        ->placeholder('Выберите тип')
                        ->helperText('Бюджетные или платные места'),

                    TextInput::make('places.count')
                        ->label('Количество мест')
                        ->required()
                        ->numeric()
                        ->minValue(0)
                        ->placeholder('Укажите количество')
                        ->helperText('Количество доступных мест'),
                ]),
            ])
            ->columns(2)
            ->maxItems(4) // Adjust if needed based on your actual requirements
            ->collapsible()
            ->collapsed()
            ->addActionLabel('Добавить группу')
            ->helperText('Добавьте группы с условиями поступления');
    }


    public static function table(Table $table): Table
    {
        return $table
            ->defaultGroup('admissionCampaign.academic_year')
            ->columns([
                Tables\Columns\TextColumn::make('educationalProgram.name')->label('Программа'),
                Tables\Columns\TextColumn::make('admissionCampaign.academic_year')->label('Приемная компания'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }




    public static function getRelations(): array
    {
        return [

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdmissionPlans::route('/'),
            'create' => Pages\CreateAdmissionPlan::route('/create'),
            'edit' => Pages\EditAdmissionPlan::route('/{record}/edit'),
        ];
    }
}
