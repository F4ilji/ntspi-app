<?php

namespace App\Filament\Components\Forms\ItemForm\CustomForm;

use App\Filament\Components\Forms\ItemForm\CustomForm\components\rules\RuleLengthLimitComponent;
use App\Filament\Components\Forms\ItemForm\CustomForm\components\rules\RuleRequiredComponent;
use App\Filament\Components\Forms\ItemForm\CustomForm\components\rules\RuleUniqueComponent;
use Filament\Forms;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class FormBuilderItem
{
    public static function getItem(): Builder
    {
        return Builder::make('columns')
            ->label('Конструктор полей формы')
            ->addActionLabel('Добавить новое поле')
            ->blockPickerColumns(3)
            ->blockPickerWidth('2xl')
            ->collapsed()
            ->collapsible()
            ->cloneable()
            ->schema([
                // Email поле
                Builder\Block::make('email')
                    ->icon('heroicon-o-envelope')
                    ->label('Поле Email')
                    ->schema([
                        TextInput::make('title_field')
                            ->label('Название поля')
                            ->placeholder('Например: Ваш Email')
                            ->helperText('Это название будет отображаться пользователям')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, string $state, Forms\Set $set) {
                                $set('name_field', Str::slug($state) . Carbon::now()->timestamp);
                            }),

                        Hidden::make('name_field')->required(),

                        Textarea::make('description')
                            ->label('Подсказка для поля')
                            ->placeholder('Например: Введите действующий email')
                            ->helperText('Необязательное пояснение для пользователей')
                            ->maxLength(500)
                            ->columnSpanFull(),

                        Section::make('Дополнительные настройки')
                            ->collapsible()
                            ->collapsed()
                            ->statePath('rules')
                            ->schema([
                                RuleRequiredComponent::getComponent(),
                                RuleUniqueComponent::getComponent(),
                                RuleLengthLimitComponent::getComponent(),
                            ]),
                    ]),

                // Phone поле
                Builder\Block::make('phone')
                    ->icon('heroicon-o-phone')
                    ->label('Поле Телефона')
                    ->schema([
                        TextInput::make('title_field')
                            ->label('Название поля')
                            ->placeholder('Например: Ваш телефон')
                            ->helperText('Укажите контактный номер для связи')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, string $state, Forms\Set $set) {
                                $set('name_field', Str::slug($state) . Carbon::now()->timestamp);
                            }),

                        Hidden::make('name_field')->required(),

                        Textarea::make('description')
                            ->label('Подсказка для поля')
                            ->placeholder('Например: +7 (XXX) XXX-XX-XX')
                            ->maxLength(500)
                            ->columnSpanFull(),

                        Section::make('Дополнительные настройки')
                            ->collapsible()
                            ->collapsed()
                            ->statePath('rules')
                            ->schema([
                                RuleRequiredComponent::getComponent(),
                                RuleUniqueComponent::getComponent(),
                                RuleLengthLimitComponent::getComponent(),
                            ]),
                    ]),

                // Короткий текст
                Builder\Block::make('text')
                    ->icon('heroicon-o-pencil')
                    ->label('Короткий текст')
                    ->schema([
                        TextInput::make('title_field')
                            ->label('Название поля')
                            ->placeholder('Например: Ваше имя')
                            ->helperText('Краткий текст (до 255 символов)')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, string $state, Forms\Set $set) {
                                $set('name_field', Str::slug($state) . Carbon::now()->timestamp);
                            }),

                        Hidden::make('name_field')->required(),

                        Textarea::make('description')
                            ->label('Подсказка для поля')
                            ->placeholder('Например: Введите ваше полное имя')
                            ->maxLength(500)
                            ->columnSpanFull(),

                        Section::make('Дополнительные настройки')
                            ->collapsible()
                            ->collapsed()
                            ->statePath('rules')
                            ->schema([
                                RuleRequiredComponent::getComponent(),
                                RuleLengthLimitComponent::getComponent(),
                            ]),
                    ]),

                // Длинный текст
                Builder\Block::make('textarea')
                    ->icon('heroicon-o-document-text')
                    ->label('Длинный текст')
                    ->schema([
                        TextInput::make('title_field')
                            ->label('Название поля')
                            ->placeholder('Например: Ваш комментарий')
                            ->helperText('Расширенный текст (до 5000 символов)')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, string $state, Forms\Set $set) {
                                $set('name_field', Str::slug($state) . Carbon::now()->timestamp);
                            }),

                        Hidden::make('name_field')->required(),

                        Textarea::make('description')
                            ->label('Подсказка для поля')
                            ->placeholder('Например: Опишите вашу проблему подробно')
                            ->maxLength(500)
                            ->columnSpanFull(),

                        Section::make('Дополнительные настройки')
                            ->collapsible()
                            ->collapsed()
                            ->statePath('rules')
                            ->schema([
                                RuleRequiredComponent::getComponent(),
                                RuleLengthLimitComponent::getComponent(),
                            ]),
                    ]),

                // Дата
                Builder\Block::make('date')
                    ->icon('heroicon-o-calendar')
                    ->label('Дата')
                    ->schema([
                        TextInput::make('title_field')
                            ->label('Название поля')
                            ->placeholder('Например: Дата рождения')
                            ->helperText('Выбор даты из календаря')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, string $state, Forms\Set $set) {
                                $set('name_field', Str::slug($state) . Carbon::now()->timestamp);
                            }),

                        Hidden::make('name_field')->required(),

                        Textarea::make('description')
                            ->label('Подсказка для поля')
                            ->placeholder('Например: Укажите вашу дату рождения')
                            ->maxLength(500)
                            ->columnSpanFull(),

                        Section::make('Дополнительные настройки')
                            ->collapsible()
                            ->collapsed()
                            ->statePath('rules')
                            ->schema([
                                RuleRequiredComponent::getComponent(),
                            ]),
                    ]),

                // Ссылка
                Builder\Block::make('url')
                    ->icon('heroicon-o-link')
                    ->label('Ссылка')
                    ->schema([
                        TextInput::make('title_field')
                            ->label('Название поля')
                            ->placeholder('Например: Ваш сайт')
                            ->helperText('Введите корректный URL адрес')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, string $state, Forms\Set $set) {
                                $set('name_field', Str::slug($state) . Carbon::now()->timestamp);
                            }),

                        Hidden::make('name_field')->required(),

                        Textarea::make('description')
                            ->label('Подсказка для поля')
                            ->placeholder('Например: https://example.com')
                            ->maxLength(500)
                            ->columnSpanFull(),

                        Section::make('Дополнительные настройки')
                            ->collapsible()
                            ->collapsed()
                            ->statePath('rules')
                            ->schema([
                                RuleRequiredComponent::getComponent(),
                                RuleUniqueComponent::getComponent(),
                                RuleLengthLimitComponent::getComponent(),
                            ]),
                    ]),

                // Множественный выбор
                Builder\Block::make('multiple_choice')
                    ->icon('heroicon-o-check-circle')
                    ->label('Множественный выбор')
                    ->schema([
                        TextInput::make('title_field')
                            ->label('Название группы')
                            ->placeholder('Например: Ваши интересы')
                            ->helperText('Несколько вариантов с возможностью выбора')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, string $state, Forms\Set $set) {
                                $set('name_field', Str::slug($state) . Carbon::now()->timestamp);
                            }),

                        Hidden::make('name_field')->required(),

                        Repeater::make('columns')
                            ->label('Варианты выбора')
                            ->addActionLabel('Добавить вариант')
                            ->collapsible()
                            ->cloneable()
                            ->itemLabel(fn (array $state): ?string => $state['title_field'] ?? 'Новый вариант')
                            ->schema([
                                TextInput::make('title_field')
                                    ->label('Текст варианта')
                                    ->placeholder('Например: Спорт')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, string $state, Forms\Set $set) {
                                        $set('name_field', Str::slug($state) . Carbon::now()->timestamp);
                                    }),

                                Hidden::make('name_field')->required(),

                                Textarea::make('description')
                                    ->label('Описание варианта')
                                    ->placeholder('Необязательное описание')
                                    ->maxLength(500),
                            ]),

                        Section::make('Дополнительные настройки')
                            ->collapsible()
                            ->collapsed()
                            ->statePath('rules')
                            ->schema([
                                RuleRequiredComponent::getComponent(),
                            ]),
                    ]),


                // Одиночный выбор
                Builder\Block::make('single_choice')
                    ->icon('heroicon-o-radio')
                    ->label('Одиночный выбор')
                    ->schema([
                        TextInput::make('title_field')
                            ->label('Название группы')
                            ->placeholder('Например: Ваш пол')
                            ->helperText('Один вариант из предложенных')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, string $state, Forms\Set $set) {
                                $set('name_field', Str::slug($state) . Carbon::now()->timestamp);
                            }),

                        Hidden::make('name_field')->required(),

                        Repeater::make('columns')
                            ->label('Варианты выбора')
                            ->addActionLabel('Добавить вариант')
                            ->collapsible()
                            ->cloneable()
                            ->itemLabel(fn (array $state): ?string => $state['title_field'] ?? 'Новый вариант')
                            ->schema([
                                TextInput::make('title_field')
                                    ->label('Текст варианта')
                                    ->placeholder('Например: Мужской')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, string $state, Forms\Set $set) {
                                        $set('name_field', Str::slug($state) . Carbon::now()->timestamp);
                                    }),

                                Hidden::make('name_field')->required(),

                                Textarea::make('description')
                                    ->label('Описание варианта')
                                    ->placeholder('Необязательное описание')
                                    ->maxLength(500),
                            ]),

                        Section::make('Дополнительные настройки')
                            ->collapsible()
                            ->collapsed()
                            ->statePath('rules')
                            ->schema([
                                RuleRequiredComponent::getComponent(),
                            ]),
                    ]),

                // Дополнительное образование
                Builder\Block::make('additional_education_choice')
                    ->icon('heroicon-o-academic-cap')
                    ->label('Доп. образование')
                    ->schema([
                        TextInput::make('title_field')
                            ->label('Название поля')
                            ->placeholder('Например: Дополнительное образование')
                            ->helperText('Выбор из списка доп. образования')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, string $state, Forms\Set $set) {
                                $set('name_field', Str::slug($state) . Carbon::now()->timestamp);
                            }),

                        Hidden::make('name_field')->required(),

                        Textarea::make('description')
                            ->label('Подсказка для поля')
                            ->placeholder('Например: Выберите интересующую программу')
                            ->maxLength(500)
                            ->columnSpanFull(),

                        Section::make('Дополнительные настройки')
                            ->collapsible()
                            ->collapsed()
                            ->statePath('rules')
                            ->schema([]),
                    ])
                    ->maxItems(1),

                // Образовательная программа
                Builder\Block::make('educational_program_choice')
                    ->icon('heroicon-o-book-open')
                    ->label('Образовательная программа')
                    ->schema([
                        TextInput::make('title_field')
                            ->label('Название поля')
                            ->placeholder('Например: Основная программа')
                            ->helperText('Выбор из списка образовательных программ')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, string $state, Forms\Set $set) {
                                $set('name_field', Str::slug($state) . Carbon::now()->timestamp);
                            }),

                        Hidden::make('name_field')->required(),

                        Textarea::make('description')
                            ->label('Подсказка для поля')
                            ->placeholder('Например: Выберите основную программу обучения')
                            ->maxLength(500)
                            ->columnSpanFull(),

                        Section::make('Дополнительные настройки')
                            ->collapsible()
                            ->collapsed()
                            ->statePath('rules')
                            ->schema([
                                RuleRequiredComponent::getComponent(),
                                RuleLengthLimitComponent::getComponent(),
                            ]),
                    ])
                    ->maxItems(1)
            ]);
    }
}