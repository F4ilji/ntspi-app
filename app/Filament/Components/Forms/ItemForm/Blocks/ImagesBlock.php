<?php

namespace App\Filament\Components\Forms\ItemForm\Blocks;

use App\Filament\Components\Forms\ItemForm\Blocks\BlockSchema;
use App\Helpers\ByteConverter;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ImagesBlock implements BlockSchema
{

    public static function schema(): array
    {
        return [
            FileUpload::make('url')
                ->label('Изображения')
                ->image()
                ->multiple()
                ->reorderable()
                ->maxFiles(5)
                ->disk('public')
                ->directory('images')
                ->imageEditor()
                ->required()
                ->helperText('Максимум 5 изображений. Можно перетаскивать для изменения порядка'),
            TextInput::make('alt')
                ->label('Описание изображений')
                ->placeholder('Необязательно')
                ->helperText('Используется для SEO и доступности'),
        ];
    }
}