<?php

namespace App\Filament\Components\Forms;

use App\Enums\CustomFormStatus;
use App\Enums\PostStatus;
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
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Components\Tab;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;
use Symfony\Component\Finder\Finder;

class PageForm
{

    public static function getPageTemplates() {

        $finder = new Finder();
        $finder->files()->in('../resources/js/Pages/Client/BasePageTemplate');


        $fileInfos = [];
        foreach ($finder as $fileInfo) {
            $fileInfos[] = [
                'filename' => $fileInfo->getBasename('.' . $fileInfo->getExtension()),

                'path' => preg_replace('/.*\/(Client\/.*)/', '$1', $fileInfo->getPath() . '/' . $fileInfo->getBasename('.' . $fileInfo->getExtension())),
                'extension' => $fileInfo->getExtension(),
                'size' => $fileInfo->getSize(),
                'mtime' => $fileInfo->getMTime(),
                'atime' => $fileInfo->getATime(),
                'isReadable' => $fileInfo->isReadable(),
                'isWritable' => $fileInfo->isWritable(),
            ];
        }
        $fileInfos = collect($fileInfos);
        return $fileInfos->pluck('filename', 'path');

    }

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
                                        ->afterStateUpdated(function (string $operation, string $state, Forms\Set $set) {
                                            $set('slug', Str::slug($state));
                                            $set('path', Str::slug($state));
                                        }),
                                    TextInput::make('slug')->label('Slug')->unique(ignoreRecord: true)->required()
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
                                TextInput::make('search_data')->hidden(),
                            ]),
                            Forms\Components\Tabs\Tab::make('Контент')->schema([
                                ContentBuilderItem::getItem()
                            ]),
                            Forms\Components\Tabs\Tab::make('Настройки')->schema([
                                Select::make('template')->options(self::getPageTemplates())->required(),
                            ]),
                        ]),


                    ])
            ]);
    }
}