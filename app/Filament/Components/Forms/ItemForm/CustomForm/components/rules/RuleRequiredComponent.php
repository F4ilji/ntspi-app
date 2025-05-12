<?php

namespace App\Filament\Components\Forms\ItemForm\CustomForm\components\rules;


use Filament\Forms\Components\Toggle;


class RuleRequiredComponent
{
    public static function getComponent() : Toggle
    {
        return Toggle::make('required')->label('Обязательное поле');
    }


}