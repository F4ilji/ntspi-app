<?php

namespace App\Filament\Components\Forms\ItemForm\Blocks;

use App\Filament\Components\Forms\ItemForm\Blocks\BlockSchema;
use App\Helpers\ByteConverter;
use App\Models\ContactWidget;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

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