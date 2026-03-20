<?php

namespace App\Filament\Components\Forms\ItemForm\CustomForm\components\rules;

use Filament\Forms;

use Filament\Forms\Components\Section;

class RuleLengthLimitComponent
{
    public static function getComponent() : Section
    {
        return Section::make('Ограничить количество символов в ответе')
            ->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('min')->label('От')->integer(),
                    Forms\Components\TextInput::make('max')->label('До')->integer()
                ]),
                Forms\Components\Toggle::make('show_length')->label('Показывать максимальное количество символов под колонкой'),
            ]);
    }


}