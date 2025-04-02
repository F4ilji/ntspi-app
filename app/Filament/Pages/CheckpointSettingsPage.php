<?php

namespace App\Filament\Pages;

use AskerAkbar\Checkpoint\Settings\CheckpointSettings;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\SettingsPage;

class CheckpointSettingsPage extends SettingsPage
{
    protected static ?string $slug = 'checkpoint/settings';


    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static string $settings = CheckpointSettings::class;

    public static function getNavigationLabel(): string
    {
        return 'Настройки Контрольных Точек'; // Русифицированная метка навигации
    }

    public function getTitle(): string
    {
        return 'Настройки Контрольных Точек'; // Заголовок страницы
    }

    public function getSubheading(): ?string
    {
        return 'Здесь вы можете настроить параметры контрольных точек.'; // Подзаголовок с описанием
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Настройки приложения'; // Группа навигации
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->columns([
                        'default' => 1,
                    ])
                    ->columnSpan(2)
                    ->schema([
                        TextInput::make('max_attempts')
                            ->required()
                            ->integer()
                            ->minValue(1)
                            ->label('Максимальное количество попыток') // Метка для поля
                            ->helperText('Введите максимальное количество попыток входа.') // Подсказка
                            ->suffix('попытки'),
                        TextInput::make('lockout_duration')
                            ->required()
                            ->integer()
                            ->minValue(1)
                            ->label('Продолжительность блокировки (в секундах)') // Метка для поля
                            ->helperText('Введите время блокировки в секундах.') // Подсказка
                            ->suffix('секунд'),
                    ]),
                Grid::make()
                    ->schema([
                        Section::make()
                            ->columns([
                                'default' => 2,
                            ])
                            ->columnSpan(2)
                            ->schema([
                                Toggle::make('notify_on_lockout')
                                    ->live()
                                    ->columnSpan(2)
                                    ->default(false)
                                    ->label('Уведомлять при блокировке'), // Метка для переключателя
                                TextInput::make('notification_emails')
                                    ->required()
                                    ->email()
                                    ->hidden(fn (Get $get): bool => ! $get('notify_on_lockout'))
                                    ->helperText('Введите адреса электронной почты для уведомлений.') // Подсказка
                                    ->label('Электронная почта для уведомлений'), // Метка для поля
                                TextInput::make('notify_after_lockouts')
                                    ->required()
                                    ->hidden(fn (Get $get): bool => ! $get('notify_on_lockout'))
                                    ->label('Уведомлять после блокировок') // Метка для поля
                                    ->helperText('Введите количество блокировок, после которых отправляется уведомление.') // Подсказка
                                    ->suffix('блокировок'),
                                TextInput::make('notification_time_frame')
                                    ->required()
                                    ->hidden(fn (Get $get): bool => ! $get('notify_on_lockout'))
                                    ->label('Временной интервал уведомлений (в секундах)') // Метка для поля
                                    ->helperText('Введите временной интервал для уведомлений в секундах.') // Подсказка
                                    ->suffix('секунд')
                            ]),
                    ]),
            ]);
    }
}