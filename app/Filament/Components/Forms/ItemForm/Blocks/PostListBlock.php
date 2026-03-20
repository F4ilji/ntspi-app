<?php

namespace App\Filament\Components\Forms\ItemForm\Blocks;

use App\Containers\Article\Models\Category;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class PostListBlock implements BlockSchema
{

    public static function schema(): array
    {
        return [
            Grid::make(2)
                ->schema([
                    TextInput::make('count')
                        ->label('Количество записей')
                        ->integer()
                        ->minValue(1)
                        ->maxValue(20)
                        ->default(5)
                        ->helperText('От 1 до 20 записей'),
                    Select::make('category')
                        ->label('Категория')
                        ->options(Category::all()->pluck('title', 'id'))
                        ->searchable()
                        ->helperText('Выберите категорию или оставьте пустым для всех'),
                ]),
        ];
    }
}