<?php

namespace App\Filament\Components\Forms;

use App\Enums\CustomFormStatus;
use App\Enums\PostStatus;
use App\Filament\Components\Forms\ItemForm\Pages\ContentBuilderItem;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Illuminate\Support\Str;
use TomatoPHP\FilamentIcons\Components\IconPicker;

class PageForm
{
    public static function getForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Forms\Components\Tabs::make('Настройки страницы')
                            ->persistTabInQueryString()
                            ->columnSpanFull()
                            ->tabs([
                                Forms\Components\Tabs\Tab::make('Основная информация')
                                    ->icon('heroicon-o-information-circle')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                TextInput::make('title')
                                                    ->label('Заголовок страницы')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->placeholder('Введите название страницы')
                                                    ->helperText('Этот заголовок будет отображаться в заголовке страницы и в навигации')
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(function (string $operation, string|null $state, Forms\Set $set) {
                                                        $set('slug', Str::slug($state));
                                                        $set('path', Str::slug($state));
                                                    }),
                                                TextInput::make('slug')
                                                    ->label('URL-адрес страницы')
                                                    ->required()
                                                    ->unique(ignoreRecord: true)
                                                    ->maxLength(255)
                                                    ->helperText('Человеко-понятный URL для страницы')
                                                    ->placeholder('example-page')
//                                                    ->prefix(fn ($record) => url('/') . '/' . substr($record->path, 0, strrpos($record->path, '/')))
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(function (string $operation, string $state, Forms\Set $set, $get) {
                                                        $set('path', Str::slug($state));
                                                    })
                                                    ->suffixAction(
                                                        Action::make('copy')
                                                            ->icon('heroicon-s-clipboard-document-check')
                                                            ->action(function ($livewire, $state, $record) {
                                                                $livewire->js(
                                                                    'window.navigator.clipboard.writeText("'. url('/') . '/' . substr($record->path, 0, strrpos($record->path, '/')) . '/' . $state.'");
                    $tooltip("'.__('Copied to clipboard').'", { timeout: 1500 });'
                                                                );
                                                            })),

                                            ]),
                                        Select::make('sub_section_id')
                                            ->label('Родительский подраздел')
                                            ->relationship('section', 'title')
                                            ->preload()
                                            ->searchable()
                                            ->placeholder('Выберите подраздел')
                                            ->helperText('Выберите раздел, к которому принадлежит эта страница')
                                            ->createOptionForm([
                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('title')
                                                            ->label('Название подраздела')
                                                            ->required()
                                                            ->maxLength(255)
                                                            ->placeholder('Введите название подраздела')
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(function (string $operation, string $state, Forms\Set $set) {
                                                                $set('slug', Str::slug($state));
                                                            }),
                                                        TextInput::make('slug')
                                                            ->label('URL подраздела')
                                                            ->unique(ignoreRecord: true)
                                                            ->readOnly()
                                                            ->required()
                                                            ->maxLength(255)
                                                            ->helperText('Автоматически генерируется из названия'),
                                                    ]),
                                            ]),
                                        Select::make('code')
                                            ->label('HTTP статус страницы')
                                            ->options([
                                                '200' => 'Обычная страница (200 OK)',
                                                '404' => 'Страница не найдена (404 Not Found)',
                                                '500' => 'Технические работы (500 Server Error)',
                                            ])
                                            ->required()
                                            ->default('200')
                                            ->helperText('Выберите HTTP статус, с которым будет отдаваться страница'),
                                        Toggle::make('searchable')
                                            ->label('Индексировать в поиске')
                                            ->default(true)
                                            ->inline(false)
                                            ->helperText('Разрешить локальному поиску индексировать страницу'),
                                        IconPicker::make('icon')
                                            ->label('Иконка страницы')
                                            ->default('heroicon-o-academic-cap')
                                            ->helperText('Выберите иконку для отображения в навигации')
                                            ->columns(6),
                                        TextInput::make('search_data')
                                            ->hidden(),
                                    ]),
                                Forms\Components\Tabs\Tab::make('Содержание')
                                    ->icon('heroicon-o-document-text')
                                    ->schema([
                                        ContentBuilderItem::getItem('content')
                                            ->helperText('Создайте содержимое страницы используя конструктор')
                                    ]),
                                Forms\Components\Tabs\Tab::make('Дополнительные настройки')
                                    ->icon('heroicon-o-cog')
                                    ->schema([
                                        Section::make('Отображение элементов')
                                            ->description('Управление видимостью элементов на странице')
                                            ->collapsible()
                                            ->schema([
                                                Toggle::make('settings.hide_page_sub_section_links')
                                                    ->label('Скрыть боковую панель с ссылками на страницы раздела')
                                                    ->helperText('Скрывает список страниц текущего раздела')
                                                    ->columnSpan(1),
                                                Toggle::make('settings.hide_page_navigate_links')
                                                    ->label('Скрыть навигацию по странице')
                                                    ->helperText('Скрывает навигацию по заголовкам')
                                                    ->columnSpan(1),
                                                Toggle::make('settings.hide_breadcrumbs')
                                                    ->label('Скрыть хлебные крошки')
                                                    ->helperText('Скрывает навигационную цепочку вверху страницы')
                                                    ->columnSpan(1),
                                            ])
                                            ->columns(2),
                                    ]),
                            ]),
                    ])
            ]);
    }
}