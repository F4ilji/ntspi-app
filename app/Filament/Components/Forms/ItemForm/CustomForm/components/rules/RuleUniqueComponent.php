<?php

namespace App\Filament\Components\Forms\ItemForm\CustomForm\components\rules;


use Filament\Forms\Components\Toggle;

class RuleUniqueComponent
{
    public static function getComponent() : Toggle
    {
        return Toggle::make('unique')->label('Уникальное поле');
    }


}