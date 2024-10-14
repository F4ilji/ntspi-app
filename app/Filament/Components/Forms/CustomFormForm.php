<?php

namespace App\Filament\Components\Forms;

use App\Enums\CustomFormStatus;
use App\Enums\PostStatus;
use App\Filament\Components\Forms\ItemForm\CustomForm\FormBuilderItem;
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

class CustomFormForm
{

    public static function getForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Tabs::make('Tabs')
                            ->tabs([
                                Tabs\Tab::make('Основная информация')
                                    ->schema([
                                        Forms\Components\Grid::make(2)->schema([
                                            TextInput::make('title')->label('Заголовок')->required()
                                                ->live(onBlur: true)
                                                ->afterStateUpdated(function (string $operation, string|null $state, Forms\Set $set) {
                                                    $set('form_id', Str::slug($state) . Carbon::now()->timestamp);
                                                }),
                                            TextInput::make('form_id')->label('ID формы')->unique(ignoreRecord: true)->required(),
                                        ]),
                                        Forms\Components\Textarea::make('description')->label('Описание формы')->required(),
                                        Select::make('status')->label('Статус формы')->required()
                                            ->options(CustomFormStatus::class)
                                    ]),
                                Tabs\Tab::make('Колонки')
                                    ->schema([
                                        FormBuilderItem::getItem(),
                                    ]),
                                Tabs\Tab::make('Кнопка отправки')
                                    ->schema([
                                        TextInput::make('button')->label('Текст кнопки отправки')->required(),
                                        Forms\Components\Textarea::make('send_message')->label('Текст после отправления письма')->required(),
                                    ]),
                                Tabs\Tab::make('Настройка интеграции с почтой')
                                    ->schema([
                                        Forms\Components\Repeater::make('mail_settings')
                                            ->label('')
                                            ->addActionLabel('Добавить получателя')
                                            ->schema([
                                                TextInput::make('target')->label('Кому')->email()->required(),
                                                TextInput::make('topic')->label('Тема')->required(),
                                                Builder::make('data')->schema([
                                                    Builder\Block::make('text')->schema([
                                                        RichEditor::make('content')->required(),
                                                    ]),
                                                    Builder\Block::make('answers')->schema([]),
                                                ])->required(),
                                            ])
                                            ->collapsed(),
                                    ]),
                            ]),
                    ])
            ]);
    }
}