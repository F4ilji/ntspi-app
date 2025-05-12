<?php

namespace App\Filament\Components\Forms\ItemForm\Blocks;

use App\Filament\Components\Forms\ItemForm\Blocks\BlockSchema;
use App\Ship\Helpers\ByteConverter;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ImageBlock implements BlockSchema
{

    public static function schema(): array
    {
        return [
            FileUpload::make('url')
                ->label('Изображение')
                ->image()
                ->multiple()
                ->reorderable()
                ->maxFiles(5)
                ->disk('public')
                ->directory('images')
                ->imageEditor()
                ->required()
                ->helperText('Можно загрузить до 5 изображений'),
            TextInput::make('alt')
                ->label('Альтернативный текст')
                ->placeholder('Необязательно')
                ->helperText('Описание изображения для SEO'),
        ];
    }
}