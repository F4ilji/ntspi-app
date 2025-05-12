<?php

namespace App\Filament\Components\Forms\ItemForm\Blocks;

use App\Filament\Components\Forms\ItemForm\Blocks\BlockSchema;
use App\Ship\Helpers\ByteConverter;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class PersonBlock implements BlockSchema
{

    public static function schema(): array
    {
        return [
            TextInput::make('name')
                ->label('Имя персоны')
                ->placeholder('Введите имя')
                ->required()
                ->maxLength(255)
                ->helperText('Полное имя персоны'),
            FileUpload::make('photo')
                ->label('Фотография')
                ->image()
                ->helperText('Рекомендуемый формат: WebP')
                ->optimize('webp')
                ->resize(50)
                ->disk('public')
                ->directory('images')
                ->imageEditor()
                ->required()
                ->downloadable()
                ->openable(),
            Repeater::make('info')
                ->label('Дополнительная информация')
                ->helperText('Добавьте характеристики персоны')
                ->schema([
                    TextInput::make('column')
                        ->label('Название характеристики')
                        ->placeholder('Например: Должность')
                        ->required()
                        ->maxLength(255),
                    Textarea::make('content')
                        ->label('Значение')
                        ->placeholder('Например: Главный инженер')
                        ->required()
                        ->maxLength(1000)
                        ->columnSpanFull(),
                ])
                ->minItems(1)
                ->grid(2)
                ->collapsible()
                ->cloneable()
                ->itemLabel(fn (array $state): ?string => $state['column'] ?? null),
        ];
    }
}