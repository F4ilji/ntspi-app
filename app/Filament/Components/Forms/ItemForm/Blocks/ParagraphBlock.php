<?php

namespace App\Filament\Components\Forms\ItemForm\Blocks;

use App\Filament\Components\Forms\ItemForm\Blocks\BlockSchema;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class ParagraphBlock implements BlockSchema
{

    public static function schema(): array
    {
        return [
            TinyEditor::make('content')
                ->label('Текст')
                ->placeholder('Начните вводить текст...')
                ->profile('test')
                ->required()
                ->helperText('Основное текстовое содержимое блока'),
        ];
    }
}
