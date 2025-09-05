<?php

namespace App\Filament\Components\Forms;

use App\Containers\Article\Enums\PostStatus;
use App\Containers\Article\Models\Category;
use App\Containers\Widget\Models\Slider;
use App\Filament\Components\Forms\ItemForm\Pages\ContentBuilderItem;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\Components\Tab;
use Illuminate\Support\Str;

class PostForm
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
                                    ->icon('heroicon-o-information-circle')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('title')
                                                    ->label('Заголовок')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->placeholder('Введите заголовок новости')
                                                    ->helperText('Этот заголовок будет отображаться на сайте')
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(function (string $operation, string|null $state, Forms\Set $set) {
                                                        $set('slug', Str::slug($state));
                                                        $set('seo.title', $state);
                                                    }),
                                                TextInput::make('slug')
                                                    ->label('URL-адрес')
                                                    ->unique(ignoreRecord: true)
                                                    ->readOnly()
                                                    ->required()
                                                    ->helperText('Этот URL будет использоваться для страницы новости')
                                                    ->maxLength(255),
                                            ]),
                                        Select::make('status')
                                            ->label('Статус публикации')
                                            ->options(PostStatus::class)
                                            ->required()
                                            ->default(PostStatus::VERIFICATION)
                                            ->helperText('Выберите статус публикации новости')
                                            ->disableOptionWhen(fn (string $value): bool =>
                                                $value == PostStatus::PUBLISHED->value && !auth()->user()->can('publish_post')
                                            ),
                                        Select::make('category_id')
                                            ->label('Категория')
                                            ->options(Category::all()->pluck('title', 'id'))
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Выберите категорию')
                                            ->helperText('Выберите категорию для новости'),
                                        SpatieTagsInput::make('tags')
                                            ->label('Теги')
                                            ->placeholder('Добавьте теги')
                                            ->helperText('Добавьте теги для лучшей классификации'),
                                        Forms\Components\TagsInput::make('authors')
                                            ->label('Авторы')
                                            ->placeholder('Добавить автора')
                                            ->helperText('Укажите авторов новости')
                                            ->suggestions([
                                                'Редакция',
                                                'Администратор',
                                            ]),
                                        Section::make('Публикация по времени')
                                            ->description('Настройте автоматическую публикацию новости в указанное время')
                                            ->collapsible()
                                            ->schema([
                                                Grid::make(2)
                                                    ->schema([
                                                        Toggle::make('publish_setting.publish_after')
                                                            ->label('Включить публикацию по времени')
                                                            ->inline(false)
                                                            ->default(false)
                                                            ->disabled(fn (Forms\Get $get) => $get('publish_setting.publish_at'))
                                                            ->live()
                                                            ->helperText('Активируйте для публикации в указанное время'),
                                                        DateTimePicker::make('publish_setting.publish_at')
                                                            ->label('Дата и время публикации')
                                                            ->displayFormat('d/m/Y H:i')
                                                            ->seconds(false)
                                                            ->helperText('Выберите дату и время публикации')
                                                            ->required(fn (Forms\Get $get) => $get('publish_setting.publish_after'))
                                                            ->disabled(fn (Forms\Get $get) => !$get('publish_setting.publish_after'))
                                                            ->native(true)
                                                            ->minDate(function (string $context, $state) {
                                                                if ($context === 'edit' && $state) {
                                                                    return $state;
                                                                } else {
                                                                    return now()->ceilHour()->subHour();
                                                                }
                                                            })
                                                            ->maxDate(now()->addMonth()),
                                                    ]),
                                            ]),
                                        Section::make('Публикация в соцсетях')
                                            ->description('Управление автоматической публикацией в социальных сетях')
                                            ->collapsible()
                                            ->schema([
                                                Grid::make()
                                                    ->schema([
                                                        Toggle::make('publication.vk')
                                                            ->label('Опубликовать в VK')
                                                            ->default(true)
                                                            ->helperText('Новость будет автоматически опубликована в VK'),
//                                                        Toggle::make('publication.telegram')
//                                                            ->label('Опубликовать в Telegram')
//                                                            ->default(true)
//                                                            ->helperText('Новость будет автоматически опубликована в Telegram'),
                                                    ]),
                                            ]),
                                    ]),
                                Tabs\Tab::make('Содержание')
                                    ->icon('heroicon-o-document-text')
                                    ->schema([
                                        ContentBuilderItem::getItem('content')
                                            ->required()
                                            ->helperText('Создайте содержимое новости используя конструктор'),
                                    ]),
                                Tabs\Tab::make('Медиа')
                                    ->icon('heroicon-o-photo')
                                    ->schema([
                                        FileUpload::make('preview')
                                            ->label('Главное изображение')
                                            ->image()
                                            ->directory('posts/previews')
                                            ->optimize('webp')
                                            ->resize(50)
                                            ->imageEditor()
                                            ->helperText('Загрузите главное изображение для новости')
                                            ->maxSize(20480)
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->imagePreviewHeight('150')
                                            ->panelLayout('integrated'),
                                        FileUpload::make('images')
                                            ->label('Галерея изображений')
                                            ->image()
                                            ->directory('posts/gallery')
                                            ->optimize('jpg')
                                            ->resize(30)
                                            ->imageEditor()
                                            ->multiple()
                                            ->reorderable()
                                            ->panelLayout('grid')
                                            ->helperText('Загрузите дополнительные изображения для галереи')
                                            ->maxFiles(200)
                                            ->maxSize(20480)
                                            ->acceptedFileTypes(['image/jpeg', 'image/png'])
                                            ->imagePreviewHeight('150'),
                                    ]),
                                Tabs\Tab::make('Слайдер')
                                    ->icon('heroicon-o-view-columns')
                                    ->schema([
                                        Toggle::make('is_slider_enabled')
                                            ->label('Добавить в слайдер')
                                            ->live()
                                            ->helperText('Активируйте для добавления новости в слайдер')
                                            ->hidden(function (Forms\Get $get, string $context) {
                                                if ($context === 'edit') {
                                                    return true;
                                                }
                                                return false;
                                            })
                                            ->dehydrated(false)
                                            ->default(false),
                                        Section::make('Настройки слайда')
                                            ->description('Настройте отображение новости в слайдере')
                                            ->collapsible()
                                            ->collapsed()
                                            ->schema([
                                                Select::make('slide.slider_id')
                                                    ->label('Выберите слайдер')
                                                    ->options(Slider::where('is_active', true)->pluck('title', 'id'))
                                                    ->required()
                                                    ->helperText('Выберите слайдер для размещения'),
                                                TextInput::make('slide.title')
                                                    ->label('Заголовок слайда')
                                                    ->maxLength(100)
                                                    ->helperText('Короткий заголовок для слайда')
                                                    ->placeholder('Введите заголовок'),
                                                Forms\Components\Textarea::make('slide.content')
                                                    ->label('Текст слайда')
                                                    ->maxLength(255)
                                                    ->helperText('Краткое описание для слайда')
                                                    ->placeholder('Введите текст слайда'),
                                                Grid::make()
                                                    ->schema([
                                                        ColorPicker::make('slide.color_theme')
                                                            ->label('Цвет текста')
                                                            ->default('#ffffff')
                                                            ->required()
                                                            ->helperText('Выберите цвет текста на слайде'),
                                                        ToggleButtons::make('slide.settings.text_position')
                                                            ->label('Позиция текста')
                                                            ->options([
                                                                'left' => 'Слева',
                                                                'center' => 'По центру',
                                                                'right' => 'Справа',
                                                            ])
                                                            ->inline()
                                                            ->grouped()
                                                            ->default('left')
                                                            ->helperText('Выберите расположение текста на слайде'),
                                                    ]),
                                                Grid::make()
                                                    ->schema([
                                                        Toggle::make('active_button')
                                                            ->label('Добавить кнопку')
                                                            ->inline(false)
                                                            ->live()
                                                            ->helperText('Добавить кнопку со ссылкой на новость')
                                                            ->afterStateHydrated(function (Toggle $component, $state, $get) {
                                                                $component->state(true);
                                                            })
                                                            ->dehydrated(false),
                                                        TextInput::make('slide.settings.link_text')
                                                            ->label('Текст кнопки')
                                                            ->default('Читать')
                                                            ->maxLength(20)
                                                            ->disabled(fn (Forms\Get $get) => !$get('active_button'))
                                                            ->helperText('Текст для кнопки перехода'),
                                                    ]),
                                                Section::make('Изображение слайда')
                                                    ->schema([
                                                        FileUpload::make('slide.image.url')
                                                            ->label('Фоновое изображение')
                                                            ->image()
                                                            ->directory('sliders')
                                                            ->optimize('webp')
                                                            ->resize(50)
                                                            ->imageEditor()
                                                            ->required()
                                                            ->maxSize(50000)
                                                            ->helperText('Загрузите фоновое изображение для слайда')
                                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']),
                                                        ToggleButtons::make('slide.image.shading')
                                                            ->label('Затемнение фона')
                                                            ->inline()
                                                            ->grouped()
                                                            ->options([
                                                                '1' => 'Нет',
                                                                '0.7' => 'Слабое',
                                                                '0.5' => 'Среднее',
                                                                '0.3' => 'Сильное',
                                                            ])
                                                            ->helperText('Выберите уровень затемнения фона'),
                                                    ]),
                                                Section::make('Время показа')
                                                    ->schema([
                                                        DateTimePicker::make('slide.end_time')
                                                            ->label('Дата окончания показа')
                                                            ->native(false)
                                                            ->displayFormat('d/m/Y H:i')
                                                            ->minDate(now())
                                                            ->maxDate(now()->addMonth())
                                                            ->helperText('Укажите до какого времени слайд будет активен'),
                                                    ]),
                                            ])
                                            ->hidden(function (Forms\Get $get) {
                                                if ($get('is_slider_enabled') === true) {
                                                    return false;
                                                }
                                                if ($get('slide')['slider_id'] !== null) {
                                                    return false;
                                                }
                                                return true;
                                            }),
                                    ])
                                    ->hidden(function (Forms\Get $get, string $context) {
                                        if ($context === 'edit' && $get('slide')['slider_id'] === null) {
                                            return true;
                                        }
                                        return false;
                                    }),
                            ])
                            ->persistTabInQueryString(),
                    ]),
            ]);
    }
}