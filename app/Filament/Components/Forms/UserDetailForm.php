<?php

namespace App\Filament\Components\Forms;

use App\Enums\CustomFormStatus;
use App\Enums\PostStatus;
use App\Filament\Components\Forms\ItemForm\Pages\ContentBuilderItem;
use App\Helpers\ByteConverter;
use App\Models\Category;
use App\Models\CustomForm;
use App\Models\Page;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Components\Tab;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;
use Symfony\Component\Finder\Finder;

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
                                ])->label('Образование'),
                                Forms\Components\Repeater::make('professionalRetraining')->schema([
                                    Forms\Components\TextInput::make('item')->label('')->string()->required()
                                ])->label('Профессиональная переподготовка'),
                                Forms\Components\Repeater::make('professionalDevelopment')
                                    ->schema([
                                        Forms\Components\TextInput::make('item')->label('')->string()->required()
                                    ])->label('Повышение квалификации'),
                                Forms\Components\Repeater::make('awards')->schema([
                                    Forms\Components\TextInput::make('item')->label('')->string()->required()
                                ])->label('Награды'),
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
                                ])->label('Преподаваемые дисциплины'),
                            ]),
                        Tabs\Tab::make('Научная деятельность')
                            ->schema([
                                Forms\Components\Repeater::make('attendedConferences')->schema([
                                    Forms\Components\TextInput::make('item')->label('')->string()->required()
                                ])->label('Участие в конференциях'),
                                Forms\Components\Repeater::make('participationScienceProjects')->schema([
                                    Forms\Components\TextInput::make('item')->label('')->string()->required()
                                ])->label('Участие в научных проектах'),
                                Forms\Components\Repeater::make('publications')->schema([
                                    Forms\Components\TextInput::make('item')->label('')->string()->required()
                                ])->label('Публикации'),
                            ]),
                        Tabs\Tab::make('Другое')
                            ->schema([
                                ContentBuilderItem::getItem('other')
                            ]),


                    ])->columnSpanFull(),
            ]);

    }
}