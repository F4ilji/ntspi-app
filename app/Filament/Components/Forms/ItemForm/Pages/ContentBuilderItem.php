<?php

namespace App\Filament\Components\Forms\ItemForm\Pages;

use App\Enums\CustomFormStatus;
use App\Enums\PostStatus;
use App\Filament\Components\Forms\ItemForm\Blocks\ContactBlock;
use App\Filament\Components\Forms\ItemForm\Blocks\CustomFormBlock;
use App\Filament\Components\Forms\ItemForm\Blocks\FilesBlock;
use App\Filament\Components\Forms\ItemForm\Blocks\HeadingBlock;
use App\Filament\Components\Forms\ItemForm\Blocks\ImagesBlock;
use App\Filament\Components\Forms\ItemForm\Blocks\PageItemBlock;
use App\Filament\Components\Forms\ItemForm\Blocks\PageResourceListBlock;
use App\Filament\Components\Forms\ItemForm\Blocks\ParagraphBlock;
use App\Filament\Components\Forms\ItemForm\Blocks\PersonBlock;
use App\Filament\Components\Forms\ItemForm\Blocks\PostItemBlock;
use App\Filament\Components\Forms\ItemForm\Blocks\PostListBlock;
use App\Filament\Components\Forms\ItemForm\Blocks\SliderBlock;
use App\Filament\Components\Forms\ItemForm\Blocks\StepperBlock;
use App\Filament\Components\Forms\ItemForm\Blocks\TabBlock;
use App\Filament\Components\Forms\ItemForm\Blocks\VideoBlock;
use App\Filament\Components\Forms\ItemForm\Defaults\TabBuilderItem;
use App\Helpers\ByteConverter;
use App\Models\Category;
use App\Models\ContactWidget;
use App\Models\CustomForm;
use App\Models\Page;
use App\Models\PageReferenceList;
use App\Models\Post;
use App\Models\Slider;
use Filament\Forms;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Fieldset;
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

class ContentBuilderItem
{
    public static function getItem(string $name): Builder
    {
        return Builder::make($name)
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

                Builder\Block::make('tabs')
                    ->label('Вкладки')
                    ->icon('heroicon-o-rectangle-stack')
                    ->schema(TabBlock::schema()),

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
            ->reorderableWithButtons();
    }


}