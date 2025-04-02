<?php

namespace App\Filament\Components\Forms\ItemForm\Blocks;

use App\Filament\Components\Forms\ItemForm\Blocks\BlockSchema;
use App\Helpers\ByteConverter;
use App\Models\Category;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

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