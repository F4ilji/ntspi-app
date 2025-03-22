<?php

namespace App\Filament\Components\Forms;

use App\Enums\CustomFormStatus;
use App\Enums\PostStatus;
use App\Filament\Components\Forms\ItemForm\Pages\ContentBuilderItem;
use App\Helpers\ByteConverter;
use App\Models\Category;
use App\Models\CustomForm;
use App\Models\Page;
use App\Models\PageReferenceList;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;
use Symfony\Component\Finder\Finder;

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
                                    ->schema([
                                        Forms\Components\Grid::make(2)->schema([
                                            TextInput::make('title')->label('Заголовок')->required()
                                                ->live(onBlur: true)
                                                ->afterStateUpdated(function (string $operation, string|null $state, Forms\Set $set) {
                                                    $set('slug', Str::slug($state));
                                                    $set('seo.title', $state);
                                                }),
                                            TextInput::make('slug')->label('Slug')->unique(ignoreRecord: true)->readOnly()->required(),
                                        ]),
                                        Select::make('status')->options(PostStatus::class)
                                            ->label('Статус')->required()
                                            ->disableOptionWhen(fn (string $value): bool =>
                                                $value == PostStatus::PUBLISHED->value && !auth()->user()->can('publish_post')
                                            )
                                            ->default(PostStatus::VERIFICATION),
                                        Select::make('category_id')
                                            ->options(Category::all()->pluck('title', 'id'))
                                            ->preload()
                                            ->label('Категория'),
                                        SpatieTagsInput::make('tags')->label('Тэги'),
                                        Forms\Components\TagsInput::make('authors')
                                            ->label('Авторы')->placeholder('Добавить автора'),
                                        Section::make('Отложенная публикация')->schema([
                                            Grid::make(2)->schema([
                                                Toggle::make('publish_setting.publish_after')
                                                    ->label('Включить')
                                                    ->inline(false)
                                                    ->default(false)
                                                    ->live(),
                                                DateTimePicker::make('publish_setting.publish_at')
                                                    ->label('Дата публикации')
                                                    ->native()
                                                    ->displayFormat('d/m/Y')

                                                    ->required(fn (Forms\Get $get) => $get('publish_setting.publish_after'))
                                                    ->disabled(fn (Forms\Get $get) => !$get('publish_setting.publish_after'))
                                                    ->minDate(Carbon::now()->subWeek())
                                                    ->maxDate(Carbon::now()->addMonth()),
                                            ]),
                                        ]),
                                        Section::make('Публикация в сервисах')->schema([
                                            Forms\Components\Grid::make()->schema([
                                                Toggle::make('publication.vk')->label('Публикация в VK')->default(true),
                                                Toggle::make('publication.telegram')->label('Публикация в Telegram')->default(true),
                                            ]),
                                        ]),
                                    ]),
                                Tabs\Tab::make('Содержание новости')
                                    ->schema([
                                        ContentBuilderItem::getItem('content')->required(),
                                    ]),
                                Tabs\Tab::make('Изображения')
                                    ->schema([
                                        FileUpload::make('preview')->label('Превью новости')
                                            ->image()
                                            ->optimize('webp')
                                            ->resize(50)
                                            ->imageEditor()
                                            ->directory('images'),
                                        FileUpload::make('images')->label('Альбом')
                                            ->image()
                                            ->optimize('jpg')
                                            ->resize(30)
                                            ->imageEditor()
                                            ->panelLayout('grid')
                                            ->reorderable()
                                            ->imageEditor()
                                            ->multiple()
                                            ->directory('images'),
                                    ]),
                                Tabs\Tab::make('Добавление новости в слайдер')
                                    ->schema([
                                        Toggle::make('is_slider_enabled')
                                            ->label('Добавить новый слайд')
                                            ->live()
                                            ->hidden(fn (string $context): bool => $context === 'edit')
                                            ->dehydrated(false)
                                            ->default(false),
                                        Section::make()
                                            ->schema([
                                                Forms\Components\Section::make('Информация слайда')->schema([
                                                    Forms\Components\TextInput::make('slide.title')
                                                        ->label('Заголовок слайда'),
                                                    Forms\Components\Textarea::make('slide.content')
                                                        ->label('Текст слайда'),
                                                    Forms\Components\Grid::make()->schema([
                                                        ColorPicker::make('slide.color_theme')
                                                            ->label('Цвет текста')
                                                            ->default('#ffffff')
                                                            ->required(),
                                                        Forms\Components\ToggleButtons::make('slide.settings.text_position')
                                                            ->options([
                                                                'left' => 'Текст слева',
                                                                'center' => 'Текст по середине',
                                                                'right' => 'Текст справа'
                                                            ])
                                                            ->inline()->default('left')->grouped()
                                                            ->label('Позиция текста на слайде'),
                                                    ]),
                                                    Forms\Components\Grid::make()->schema([
                                                        Toggle::make('active_button')
                                                            ->label('Использовать кнопку для ссылки (Ссылка будет открываться при нажатии на слайд)')
                                                            ->inline(false)
                                                            ->live()
                                                            ->afterStateHydrated(function (Toggle $component, $state, $get) {
                                                                $component->state(true);
                                                            })
                                                            ->dehydrated(false),
                                                        Forms\Components\TextInput::make('slide.settings.link_text')
                                                            ->default('Читать')
                                                            ->label('Текст кнопки')
                                                            ->disabled(fn (Forms\Get $get) => !$get('active_button'))
                                                    ]),
                                                ]),
                                                Forms\Components\Section::make('Изображение')->schema([
                                                    FileUpload::make('slide.image.url')
                                                        ->label('Изображение')
                                                        ->image()
                                                        ->optimize('webp')
                                                        ->resize(50)
                                                        ->disk('public')
                                                        ->directory('images')
                                                        ->imageEditor()
                                                        ->required(),
                                                    ToggleButtons::make('slide.image.shading')->inline()->grouped()->label('Уровень затемнения изображения')->options([
                                                        '1' => 'Без затемнения',
                                                        '0.7' => 'Слабое затемнение',
                                                        '0.5' => 'Среднее затемнение',
                                                        '0.3' => 'Сильное затемнение',
                                                    ]),
                                                ]),
                                                Forms\Components\Section::make('Общая часть')->schema([
                                                    Forms\Components\Grid::make()->schema([
                                                        DateTimePicker::make('slide.end_time')
                                                            ->label('Слайд действует до')
                                                            ->native()
                                                            ->displayFormat('d/m/Y')
                                                            ->minDate(Carbon::now())
                                                            ->maxDate(Carbon::now()->addMonth()),
                                                    ]),
                                                ]),
                                            ])
                                            ->hidden(fn(Forms\Get $get) => !$get('is_slider_enabled'))


                                    ]),
                            ]),
                    ])
            ]);
    }
}