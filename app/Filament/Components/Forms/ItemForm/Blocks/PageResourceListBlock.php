<?php

namespace App\Filament\Components\Forms\ItemForm\Blocks;

use App\Filament\Components\Forms\ItemForm\Blocks\BlockSchema;
use App\Helpers\ByteConverter;
use App\Models\PageReferenceList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

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