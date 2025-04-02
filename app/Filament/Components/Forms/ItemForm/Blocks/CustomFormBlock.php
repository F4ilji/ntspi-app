<?php

namespace App\Filament\Components\Forms\ItemForm\Blocks;

use App\Enums\CustomFormStatus;
use App\Models\CustomForm;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;


class CustomFormBlock implements BlockSchema
{
    public static function schema(): array
    {
        return [
            Select::make('form')
                ->label('Форма')
                ->options(CustomForm::query()->where('status', CustomFormStatus::PUBLISHED)->pluck('title', 'form_id'))
                ->searchable()
                ->required()
                ->helperText('Выберите опубликованную форму'),
            Section::make()->schema([
                Toggle::make('settings.in_modal')->label('Открывать в модальном окне')->default(false),
            ]),
        ];
    }
}