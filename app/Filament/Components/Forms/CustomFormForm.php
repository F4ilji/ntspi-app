<?php

namespace App\Filament\Components\Forms;

use App\Containers\Widget\Enums\CustomFormStatus;
use App\Filament\Components\Forms\ItemForm\CustomForm\FormBuilderItem;
use Filament\Forms;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\DateTimePicker;
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

class CustomFormForm
{

    public static function getForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Настройки формы')
                    ->description('Конфигурация пользовательской формы')
                    ->collapsible()
                    ->schema([
                        Tabs::make('Конфигурация формы')
                            ->persistTabInQueryString()
                            ->columnSpanFull()
                            ->tabs([
                                Tabs\Tab::make('Основная информация')
                                    ->icon('heroicon-o-information-circle')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                TextInput::make('title')
                                                    ->label('Название формы')
                                                    ->placeholder('Введите название формы')
                                                    ->helperText('Это название будет видно пользователям')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(function (string $operation, string|null $state, Forms\Set $set) {
                                                        $set('form_id', Str::slug($state) . Carbon::now()->timestamp);
                                                    }),

                                                TextInput::make('form_id')
                                                    ->label('Уникальный ID формы')
                                                    ->helperText('Автоматически генерируется из названия')
                                                    ->required()
                                                    ->unique(ignoreRecord: true)
                                                    ->maxLength(255),
                                            ]),

                                        Forms\Components\Textarea::make('description')
                                            ->label('Описание формы')
                                            ->placeholder('Опишите назначение этой формы')
                                            ->helperText('Это описание будет видно пользователям')
                                            ->required()
                                            ->maxLength(2000)
                                            ->columnSpanFull(),

                                        Select::make('status')
                                            ->label('Статус формы')
                                            ->options(CustomFormStatus::class)
                                            ->required()
                                            ->native(false)
                                            ->helperText('Определяет видимость формы на сайте')
                                            ->columnSpanFull(),
                                    ]),

                                Tabs\Tab::make('Поля формы')
                                    ->icon('heroicon-o-view-columns')
                                    ->schema([
                                        FormBuilderItem::getItem()
                                            ->columnSpanFull(),
                                    ]),

                                Tabs\Tab::make('Кнопка отправки')
                                    ->icon('heroicon-o-paper-airplane')
                                    ->schema([
                                        TextInput::make('button')
                                            ->label('Текст кнопки отправки')
                                            ->placeholder('Например: Отправить заявку')
                                            ->helperText('Текст, который будет отображаться на кнопке отправки формы')
                                            ->required()
                                            ->maxLength(255),

                                        Forms\Components\Textarea::make('send_message')
                                            ->label('Сообщение после отправки')
                                            ->placeholder('Спасибо! Ваша заявка принята.')
                                            ->helperText('Это сообщение увидят пользователи после успешной отправки формы')
                                            ->required()
                                            ->maxLength(1000)
                                            ->columnSpanFull(),
                                    ]),

                                Tabs\Tab::make('Настройки')
                                    ->icon('heroicon-o-cog')
                                    ->schema([
                                        Toggle::make('settings.personal_data')
                                            ->label('Согласие на обработку данных')
                                            ->helperText('Показывать checkbox для согласия на обработку персональных данных')
                                            ->inline(false)
                                            ->onColor('success')
                                            ->offColor('gray'),

                                        Toggle::make('settings.captcha')
                                            ->label('Защита CAPTCHA')
                                            ->helperText('Включить защиту от спама с помощью CAPTCHA')
                                            ->inline(false)
                                            ->onColor('success')
                                            ->offColor('gray'),

                                        Section::make('Ограничение по времени')
                                            ->collapsible()
                                            ->schema([
                                                Toggle::make('is_time_period')
                                                    ->label('Ограничить период работы формы')
                                                    ->helperText('Форма будет активна только в указанный период')
                                                    ->dehydrated(false)
                                                    ->live(true)
                                                    ->inline(false),

                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        DateTimePicker::make('settings.period.start_time')
                                                            ->label('Дата начала')
                                                            ->native(false)
                                                            ->displayFormat('d/m/Y H:i')
                                                            ->seconds(false)
                                                            ->helperText('Когда форма станет доступна')
                                                            ->default(Carbon::now())
                                                            ->minDate(Carbon::now()),

                                                        DateTimePicker::make('settings.period.end_time')
                                                            ->label('Дата окончания')
                                                            ->native(false)
                                                            ->displayFormat('d/m/Y H:i')
                                                            ->seconds(false)
                                                            ->helperText('Когда форма перестанет быть доступна')
                                                            ->default(Carbon::now()->addWeeks(2))
                                                            ->minDate(Carbon::now()),
                                                    ])
                                                    ->hidden(fn(Forms\Get $get): bool => $get('is_time_period') !== true),
                                            ]),
                                    ]),

                                Tabs\Tab::make('Настройки почты')
                                    ->icon('heroicon-o-envelope')
                                    ->schema([
                                        Forms\Components\Repeater::make('mail_settings')
                                            ->label('Настройки уведомлений')
                                            ->addActionLabel('Добавить получателя')
                                            ->helperText('Укажите, кому и какие уведомления отправлять')
                                            ->collapsed()
                                            ->itemLabel(fn (array $state): ?string => $state['target'] ?? 'Новый получатель')
                                            ->schema([
                                                TextInput::make('target')
                                                    ->label('Email получателя')
                                                    ->placeholder('email@example.com')
                                                    ->email()
                                                    ->required()
                                                    ->maxLength(255),

                                                TextInput::make('topic')
                                                    ->label('Тема письма')
                                                    ->placeholder('Новая заявка с формы')
                                                    ->required()
                                                    ->maxLength(255),

                                                Builder::make('data')
                                                    ->label('Содержимое письма')
                                                    ->blockNumbers(false)
                                                    ->collapsible()
                                                    ->schema([
                                                        Builder\Block::make('text')
                                                            ->label('Текст письма')
                                                            ->schema([
                                                                RichEditor::make('content')
                                                                    ->label('')
                                                                    ->required()
                                                                    ->toolbarButtons([
                                                                        'bold', 'italic', 'link',
                                                                        'orderedList', 'bulletList'
                                                                    ]),
                                                            ]),
                                                        Builder\Block::make('answers')
                                                            ->label('Ответы формы')
                                                            ->schema([]),
                                                    ]),
                                            ])
                                            ->grid(2),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}