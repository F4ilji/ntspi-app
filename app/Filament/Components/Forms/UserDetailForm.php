<?php

namespace App\Filament\Components\Forms;

use App\Filament\Components\Forms\ItemForm\Pages\ContentBuilderItem;
use Filament\Forms;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Resources\Components\Tab;

class UserDetailForm
{


    public static function getForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\Toggle::make('is_only_worker')
                            ->inline(false)
                            ->label('Только сотрудник')
                    ]),
                ]),
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Основная информация')
                            ->schema([
                                Forms\Components\FileUpload::make('photo')
                                    ->label('Фотография')
                                    ->image()
//                                    ->optimize('jpg')
//                                    ->resize(30)
                                    ->disk('public')
                                    ->imageEditor()
                                    ->directory('images')
                                    ->imageEditor(),
                                Forms\Components\Grid::make()->schema([
                                    Forms\Components\TextInput::make('contactEmail')
                                        ->label('Контактный Email')
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('contactPhone')
                                        ->label('Контактный телефон')
                                        ->maxLength(255),
                                ]),
                            ]),
                        Tabs\Tab::make('Образование')
                            ->schema([
                                Forms\Components\Grid::make()->schema([
                                    Forms\Components\TextInput::make('academicTitle')
                                        ->label('Ученая степень')
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('AcademicDegree')
                                        ->label('Ученое звание')
                                        ->maxLength(255),
                                ]),
                                Forms\Components\Repeater::make('education')->schema([
                                    Forms\Components\TextInput::make('year')->label('Года обучения')->string()->required(),
                                    Forms\Components\TextInput::make('content')->label('Общая информация')->string()->required(),
                                    Forms\Components\TextInput::make('institution')->label('Учебное заведение')->string()->required()
                                ])->label('Образование')->defaultItems(0),
                                Forms\Components\Repeater::make('professionalRetraining')->schema([
                                    Forms\Components\TextInput::make('item')->label('')->string()->required()
                                ])->label('Профессиональная переподготовка')->defaultItems(0),
                                Forms\Components\Repeater::make('professionalDevelopment')
                                    ->schema([
                                        Forms\Components\TextInput::make('item')->label('')->string()->required()
                                    ])->label('Повышение квалификации')->defaultItems(0),
                                Forms\Components\Repeater::make('awards')->schema([
                                    Forms\Components\TextInput::make('item')->label('')->string()->required()
                                ])->label('Награды')->defaultItems(0),
                            ]),
                        Tabs\Tab::make('Преподавание')
                            ->schema([
                                Forms\Components\Grid::make(2)->schema([
                                    Forms\Components\TextInput::make('workExperience.total')
                                        ->label('Стаж работы в общем')
                                        ->numeric(),
                                    Forms\Components\TextInput::make('workExperience.byProf')
                                        ->label('Стаж работы по специальности')
                                        ->numeric(),
                                ]),
                                Forms\Components\Repeater::make('professDisciplines')->schema([
                                    Forms\Components\TextInput::make('item')->label('')->string()->required()
                                ])->label('Преподаваемые дисциплины')->defaultItems(0),
                            ]),
                        Tabs\Tab::make('Научная деятельность')
                            ->schema([
                                Forms\Components\Repeater::make('attendedConferences')->schema([
                                    Forms\Components\TextInput::make('item')->label('')->string()->required()
                                ])->label('Участие в выставках, конференциях, проектах')->defaultItems(0),
                                Forms\Components\Repeater::make('publications')->schema([
                                    Forms\Components\TextInput::make('category_publication')->label('Категория публикаций')->string()->required(),
                                    Forms\Components\Repeater::make('publication')->schema([
                                        Forms\Components\TextInput::make('item')->label('')->string()->required()
                                    ])->label('Публикации')->collapsed(),
                                ])->label('Публикации')->defaultItems(0),
                            ]),
                        Tabs\Tab::make('Другое')
                            ->schema([
                                ContentBuilderItem::getItem('other')
                            ]),


                    ])->columnSpanFull(),
            ]);

    }
}