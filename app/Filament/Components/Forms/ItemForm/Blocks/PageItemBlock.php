<?php

namespace App\Filament\Components\Forms\ItemForm\Blocks;

use App\Filament\Components\Forms\ItemForm\Blocks\BlockSchema;
use App\Ship\Helpers\ByteConverter;
use App\Containers\AppStructure\Models\Page;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class PageItemBlock implements BlockSchema
{

    public static function schema(): array
    {
        return [
            Select::make('page')
                ->label('Страница')
                ->options(Page::query()->where('title', '!=', null)->where('is_visible', true)->pluck('title', 'id'))
                ->searchable()
                ->required()
                ->helperText('Выберите видимую страницу'),
        ];
    }
}