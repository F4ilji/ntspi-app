<?php

namespace App\Filament\Components\Forms\ItemForm\Blocks;

use App\Filament\Components\Forms\ItemForm\Blocks\BlockSchema;
use App\Filament\Components\Forms\ItemForm\Defaults\TabBuilderItem;
use App\Helpers\ByteConverter;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class TabBlock implements BlockSchema
{

    public static function schema(): array
    {
        return [
            Repeater::make('tab')
                ->label('Вкладки')
                ->helperText('Добавьте вкладки с контентом')
                ->schema([
                    TextInput::make('title')
                        ->label('Название вкладки')
                        ->placeholder('Введите название вкладки')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull()
                        ->helperText('Это название будет отображаться в табе'),
                    Builder::make('content')
                        ->label('')
                        ->blocks([
                            Builder\Block::make('heading')
                                ->label('Заголовок')
                                ->icon('heroicon-o-hashtag')
                                ->schema(HeadingBlock::schema()),

                            Builder\Block::make('paragraph')
                                ->label('Текст')
                                ->icon('heroicon-o-document-text')
                                ->schema(ParagraphBlock::schema()),

                            Builder\Block::make('files')
                                ->label('Файлы')
                                ->icon('heroicon-o-paper-clip')
                                ->schema(FilesBlock::schema()),

                            Builder\Block::make('person')
                                ->label('Персона')
                                ->icon('heroicon-o-user')
                                ->schema(PersonBlock::schema()),

                            Builder\Block::make('stepper')
                                ->label('Этапы')
                                ->icon('heroicon-o-list-bullet')
                                ->schema(StepperBlock::schema()),

                            Builder\Block::make('images')
                                ->label('Слайдер изображений')
                                ->icon('heroicon-o-photo')
                                ->schema(ImagesBlock::schema()),

                            Builder\Block::make('image')
                                ->label('Изображение')
                                ->icon('heroicon-o-photo')
                                ->schema(ImagesBlock::schema()),

                            Builder\Block::make('video')
                                ->label('Видео')
                                ->icon('heroicon-o-film')
                                ->schema(VideoBlock::schema()),

                            Builder\Block::make('postsList')
                                ->label('Список новостей')
                                ->icon('heroicon-o-newspaper')
                                ->schema(PostListBlock::schema()),

                            Builder\Block::make('postItem')
                                ->label('Конкретная новость')
                                ->icon('heroicon-o-document-text')
                                ->schema(PostItemBlock::schema()),

                            Builder\Block::make('pageItem')
                                ->label('Конкретная страница')
                                ->icon('heroicon-o-document')
                                ->schema(PageItemBlock::schema()),

                            Builder\Block::make('customForm')
                                ->label('Пользовательская форма')
                                ->icon('heroicon-o-clipboard-document-list')
                                ->schema(CustomFormBlock::schema()),

                            Builder\Block::make('pageResourceList')
                                ->label('Ресурсы')
                                ->icon('heroicon-o-archive-box')
                                ->schema(PageResourceListBlock::schema()),

                            Builder\Block::make('contact')
                                ->label('Контакты')
                                ->icon('heroicon-o-phone')
                                ->schema(ContactBlock::schema()),

                            Builder\Block::make('slider')
                                ->label('Слайдер')
                                ->icon('heroicon-o-presentation-chart-line')
                                ->schema(SliderBlock::schema()),
                        ])
                        ->collapsed()
                        ->blockNumbers(false)
                        ->collapsible()
                        ->blockPickerColumns(3)
                        ->blockPickerWidth('2xl')
                        ->addActionLabel('Добавить новый блок')
                        ->cloneable()
                        ->reorderableWithButtons(),
                    ])
                ->minItems(1)
                ->collapsible()
                ->cloneable()

                ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
        ];
    }
}