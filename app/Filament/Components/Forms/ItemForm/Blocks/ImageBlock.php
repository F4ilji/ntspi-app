<?php

namespace App\Filament\Components\Forms\ItemForm\Blocks;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;

class ImageBlock implements BlockSchema
{

    public static function schema(): array
    {
        return [
            FileUpload::make('url')
                ->label('Изображение')
                ->image()
                ->disk('public')
                ->directory('images')
                ->imageEditor()
                ->required()
                ->helperText('Выберите или перетащите изображение'),
            TextInput::make('alt')
                ->label('Описание изображений')
                ->placeholder('Необязательно')
                ->helperText('Используется для SEO и доступности'),
        ];
    }
}