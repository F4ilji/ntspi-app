<?php

namespace App\Filament\Components\Forms\ItemForm\Blocks;


use App\Containers\Widget\Models\PageReferenceList;
use Filament\Forms\Components\Select;

class PageResourceListBlock implements BlockSchema
{
    public static function schema(): array
    {
        return [
            Select::make('resource')
                ->label('Ресурс')
                ->options(PageReferenceList::query()->where('is_active', true)->pluck('title', 'slug'))
                ->searchable()
                ->required()
                ->helperText('Выберите активный ресурс'),
        ];
    }
}