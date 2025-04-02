<?php

namespace App\Filament\Components\Forms\ItemForm\Blocks;

use App\Filament\Components\Forms\ItemForm\Blocks\BlockSchema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class ParagraphBlock implements BlockSchema
{

    public static function schema(): array
    {
        return [
            Toggle::make('seo_active')
                ->label('Использовать блок как SEO-текст')
                ->helperText('Этот текст будет использоваться для SEO-оптимизации')
                ->live(onBlur: true)
                ->required()
                ->disabled(function ($state, Get $get) {
                    $data = $get('../../');
                    return self::findSeoActive($data) && !$state;
                })
                ->dehydrated(),
            TinyEditor::make('content')
                ->label('Текст')
                ->placeholder('Начните вводить текст...')
                ->profile('test')
                ->required()
                ->helperText('Основное текстовое содержимое блока'),
        ];
    }

    private static function findSeoActive(array $data) : bool
    {
        $bool = false;

        foreach ($data as $item) {
            if ($item['type'] !== 'paragraph') {
                continue;
            }
            if ($item['data']['seo_active'] === true) {
                $bool = true;
                break;
            }
        }
        return $bool;
    }
}