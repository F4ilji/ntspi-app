<?php

namespace App\Filament\Components\Forms\ItemForm\Blocks;

use App\Containers\Widget\Models\Slider;
use Filament\Forms\Components\Select;

class SliderBlock implements BlockSchema
{

    public static function schema(): array
    {
        return [
            Select::make('slider')
                ->label('Слайдер')
                ->options(Slider::query()->whereHas('slides')->where('is_active', true)->pluck('title', 'slug'))
                ->searchable()
                ->required()
                ->helperText('Выберите активный слайдер с изображениями'),
        ];
    }
}