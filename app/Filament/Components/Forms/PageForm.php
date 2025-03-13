<?php

namespace App\Filament\Components\Forms;

use App\Enums\CustomFormStatus;
use App\Enums\PostStatus;
use App\Filament\Components\Forms\ItemForm\Pages\ContentBuilderItem;
use Filament\Forms;
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
                        Forms\Components\Tabs::make('')->schema([
                            Forms\Components\Tabs\Tab::make('Основная информация')->schema([
                                Forms\Components\Grid::make(2)->schema([
                                    TextInput::make('title')->label('Заголовок')->required()
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function (string $operation, string|null $state, Forms\Set $set) {
                                            $set('slug', Str::slug($state));
                                            $set('path', Str::slug($state));
                                        }),
                                    TextInput::make('slug')->label('Текстовый идентификатор страницы')->unique(ignoreRecord: true)->required()
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function (string $operation, string $state, Forms\Set $set, $get) {
                                            $set('path', Str::slug($state));
                                        }),
                                ]),
                                Select::make('sub_section_id')->label('Подраздел')
                                    ->relationship('section', 'title')
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('title')->label('Название подраздела')->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (string $operation, string $state, Forms\Set $set) {
                                                $set('slug', Str::slug($state));
                                            }),
                                        TextInput::make('slug')->label('Slug')->unique(ignoreRecord: true)->readOnly()->required(),
                                    ]),
                                Select::make('code')->options([
                                    '200' => 'Открытая страница',
                                    '404' => 'Не найдено',
                                    '500' => 'Ведутся технические работы',
                                ])->label('Статус')->required()->default('200'),
                                Toggle::make('searchable')->default(true)->label('Индексируется поиском')->inline(false),
                                IconPicker::make('icon')
                                    ->default('heroicon-o-academic-cap')
                                    ->label('Icon'),


                                TextInput::make('search_data')->hidden(),
                            ]),
                            Forms\Components\Tabs\Tab::make('Контент')->schema([
                                ContentBuilderItem::getItem('content')
                            ]),
                            Forms\Components\Tabs\Tab::make('Настройки')->schema([
                                Section::make()->schema([
                                    Toggle::make('settings.hide_page_sub_section_links')
                                        ->label('Скрыть сайдбар смежных страниц')
                                        ->columnSpan(1),

                                    Toggle::make('settings.hide_page_navigate_links')
                                        ->label('Скрыть навигацию по страницу')
                                        ->columnSpan(1),

                                    Toggle::make('settings.hide_breadcrumbs')
                                        ->label('Скрыть хлебные крошки')
                                        ->columnSpan(1),
//
//                                    Toggle::make('settings.full_width_page')
//                                        ->label('Страница на всю ширину')
//                                        ->columnSpan(1)->default(true),
                                ]),
                            ]),
                        ]),


                    ])
            ]);
    }
}