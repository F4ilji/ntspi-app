<?php

namespace App\Filament\Components\Forms\ItemForm\CustomForm;

use App\Enums\CustomFormStatus;
use App\Enums\PostStatus;
use App\Filament\Components\Forms\ItemForm\CustomForm\components\rules\RuleLengthLimitComponent;
use App\Filament\Components\Forms\ItemForm\CustomForm\components\rules\RuleRequiredComponent;
use App\Filament\Components\Forms\ItemForm\CustomForm\components\rules\RuleUniqueComponent;
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
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class FormBuilderItem
{
    public static function getItem()
    {
        return Builder::make('columns')
            ->schema([
                Builder\Block::make('email')
                    ->label('Почта')
                    ->schema([
                        TextInput::make('title_field')
                            ->label('Заголовок поля')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, string $state, Forms\Set $set) {
                                $set('name_field', Str::slug($state) . Carbon::now()->timestamp );
                            }),
                        Forms\Components\Hidden::make('name_field')->required(),
                        Forms\Components\Textarea::make('description')->label('Описание поля (опционально)'),

                        Section::make('Настройка')
                            ->collapsed()
                            ->statePath('rules')
                            ->schema([
                                RuleRequiredComponent::getComponent(),
                                RuleUniqueComponent::getComponent(),
                                RuleLengthLimitComponent::getComponent(),
                            ]),
                    ]),
                Builder\Block::make('phone')
                    ->label('Телефон')
                    ->schema([
                        TextInput::make('title_field')
                            ->label('Заголовок поля')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, string $state, Forms\Set $set) {
                                $set('name_field', Str::slug($state) . Carbon::now()->timestamp );
                            }),
                        Forms\Components\Hidden::make('name_field')->required(),
                        Forms\Components\Textarea::make('description')->label('Описание поля (опционально)'),
                        Section::make('Настройка')
                            ->statePath('rules')
                            ->schema([
                                RuleRequiredComponent::getComponent(),
                                RuleUniqueComponent::getComponent(),
                                RuleLengthLimitComponent::getComponent(),
                            ]),
                    ]),
                Builder\Block::make('text')
                    ->label('Короткий текст')
                    ->schema([
                        TextInput::make('title_field')
                            ->label('Заголовок поля')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, string $state, Forms\Set $set) {
                                $set('name_field', Str::slug($state) . Carbon::now()->timestamp );
                            }),
                        Forms\Components\Hidden::make('name_field')->required(),
                        Forms\Components\Textarea::make('description')->label('Описание поля (опционально)'),

                        Section::make('Настройка')
                            ->statePath('rules')
                            ->schema([
                                RuleRequiredComponent::getComponent(),
                                RuleLengthLimitComponent::getComponent(),
                            ]),
                        ]),
                Builder\Block::make('textarea')
                    ->label('Длинный текст текст')
                    ->schema([
                        TextInput::make('title_field')
                            ->label('Заголовок поля')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, string $state, Forms\Set $set) {
                                $set('name_field', Str::slug($state) . Carbon::now()->timestamp );
                            }),
                        Forms\Components\Hidden::make('name_field')->required(),
                        Forms\Components\Textarea::make('description')->label('Описание поля (опционально)'),
                        Section::make('Настройка')
                            ->statePath('rules')
                            ->schema([
                                RuleRequiredComponent::getComponent(),
                                RuleLengthLimitComponent::getComponent(),
                            ]),
                    ]),
                Builder\Block::make('date')
                    ->label('Дата')
                    ->schema([
                        TextInput::make('title_field')
                            ->label('Заголовок поля')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, string $state, Forms\Set $set) {
                                $set('name_field', Str::slug($state) . Carbon::now()->timestamp );
                            }),
                        Forms\Components\Hidden::make('name_field')->required(),
                        Forms\Components\Textarea::make('description')->label('Описание поля (опционально)'),
                        Section::make('Настройка')
                            ->statePath('rules')
                            ->schema([
                                RuleRequiredComponent::getComponent(),
                            ]),

                    ]),
                Builder\Block::make('url')
                    ->label('Ссылка')
                    ->schema([
                        TextInput::make('title_field')
                            ->label('Заголовок поля')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, string $state, Forms\Set $set) {
                                $set('name_field', Str::slug($state) . Carbon::now()->timestamp );
                            }),
                        Forms\Components\Hidden::make('name_field')->required(),
                        Forms\Components\Textarea::make('description')->label('Описание поля (опционально)'),
                        Section::make('Настройка')
                            ->statePath('rules')
                            ->schema([
                                RuleRequiredComponent::getComponent(),
                                RuleUniqueComponent::getComponent(),
                                RuleLengthLimitComponent::getComponent(),
                            ]),
                    ]),
                Builder\Block::make('multiple_choice')
                    ->label('Несколько вариантов')
                    ->schema([
                        TextInput::make('title_field')
                            ->label('Заголовок поля')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, string $state, Forms\Set $set) {
                                $set('name_field', Str::slug($state) . Carbon::now()->timestamp );
                            }),
                        Forms\Components\Hidden::make('name_field')->required(),                                                        Forms\Components\Repeater::make('columns')->schema([
                            TextInput::make('title_field')
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (string $operation, string $state, Forms\Set $set) {
                                    $set('name_field', Str::slug($state) . Carbon::now()->timestamp );
                                }),
                            Forms\Components\Hidden::make('name_field')->required(),
                            Forms\Components\Textarea::make('description')->label('Описание поля (опционально)'),
                        ])->collapsed(),

                        Section::make('Настройка')
                            ->statePath('rules')
                            ->schema([
                                RuleRequiredComponent::getComponent(),
                            ]),
                    ]),
                Builder\Block::make('single_choice')
                    ->label('Один вариант')
                    ->schema([
                        TextInput::make('title_field')
                            ->label('Заголовок поля')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, string $state, Forms\Set $set) {
                                $set('name_field', Str::slug($state) . Carbon::now()->timestamp );
                            }),
                        Forms\Components\Hidden::make('name_field')->required(),                                                        Forms\Components\Repeater::make('columns')->schema([
                            TextInput::make('title_field')
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (string $operation, string $state, Forms\Set $set) {
                                    $set('name_field', Str::slug($state) . Carbon::now()->timestamp );
                                }),
                            Forms\Components\Hidden::make('name_field')->required(),
                            Forms\Components\Textarea::make('description')->label('Описание поля (опционально)'),
                        ])->collapsed(),

                        Section::make('Настройка')
                            ->statePath('rules')
                            ->schema([
                                RuleRequiredComponent::getComponent(),
                           ]),
                    ]),
                Builder\Block::make('additional_education_choice')
                    ->label('Выбрать дополнительное образование')
                    ->schema([
                        TextInput::make('title_field')
                            ->label('Заголовок поля')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, string $state, Forms\Set $set) {
                                $set('name_field', Str::slug($state) . Carbon::now()->timestamp );
                            }),
                        Forms\Components\Hidden::make('name_field')->required(),
                        Forms\Components\Textarea::make('description')->label('Описание поля (опционально)'),

                        Section::make('Настройка')
                            ->statePath('rules')
                            ->schema([
//                                RuleRequiredComponent::getComponent(),
//                                RuleLengthLimitComponent::getComponent(),
                            ]),
                    ])
                    ->maxItems(1),
                Builder\Block::make('educational_program_choice')
                    ->label('Выбрать Образовательную программу')
                    ->schema([
                        TextInput::make('title_field')
                            ->label('Заголовок поля')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, string $state, Forms\Set $set) {
                                $set('name_field', Str::slug($state) . Carbon::now()->timestamp );
                            }),
                        Forms\Components\Hidden::make('name_field')->required(),
                        Forms\Components\Textarea::make('description')->label('Описание поля (опционально)'),

                        Section::make('Настройка')
                            ->statePath('rules')
                            ->schema([
                                RuleRequiredComponent::getComponent(),
                                RuleLengthLimitComponent::getComponent(),
                            ]),
                    ])
                    ->maxItems(1),
            ])
            ->label('')
            ->addActionLabel('Добавить поле')
            ->blockPickerColumns(3)
            ->blockPickerWidth('2xl')
            ->collapsed();

    }


}