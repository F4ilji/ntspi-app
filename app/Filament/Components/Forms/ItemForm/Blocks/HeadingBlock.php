<?php

namespace App\Filament\Components\Forms\ItemForm\Blocks;

use App\Filament\Components\Forms\ItemForm\Blocks\BlockSchema;
use Filament\Forms\Components\TextInput;

class HeadingBlock implements BlockSchema
{

    public static function schema(): array
    {
        return [
            TextInput::make('id')
                ->hidden()
                ->integer()
                ->default(rand(2335235, 324634264263426)),
            TextInput::make('content')
                ->label('Текст заголовка')
                ->placeholder('Введите текст заголовка')
                ->helperText('Основной заголовок раздела')
                ->live(onBlur: true)
                ->required()
                ->maxLength(255),
        ];
    }
}