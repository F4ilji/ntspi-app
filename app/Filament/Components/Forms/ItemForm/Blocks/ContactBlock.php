<?php

namespace App\Filament\Components\Forms\ItemForm\Blocks;


use App\Containers\Widget\Models\ContactWidget;
use Filament\Forms\Components\Select;

class ContactBlock implements BlockSchema
{

    public static function schema(): array
    {
        return [
            Select::make('contact')
                ->label('Виджет контактов')
                ->options(ContactWidget::query()->where('is_active', true)->pluck('title', 'slug'))
                ->searchable()
                ->required()
                ->helperText('Выберите активный виджет контактов'),
        ];
    }
}