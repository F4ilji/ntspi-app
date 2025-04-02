<?php

namespace App\Filament\Components\Forms\ItemForm\Blocks;

use App\Filament\Components\Forms\ItemForm\Blocks\BlockSchema;
use App\Helpers\ByteConverter;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class StepperBlock implements BlockSchema
{

    public static function schema(): array
    {
        return [
            TextInput::make('step_name')
                ->label('Название процесса')
                ->placeholder('Например: Процесс оформления')
                ->required()
                ->maxLength(255)
                ->helperText('Общее название для всех шагов'),
            Repeater::make('steps')
                ->label('Шаги')
                ->helperText('Добавьте шаги процесса')
                ->schema([
                    TextInput::make('title')
                        ->label('Название шага')
                        ->placeholder('Например: Шаг 1')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    RichEditor::make('content')
                        ->label('Описание шага')
                        ->required()
                        ->toolbarButtons([
                            'bold',
                            'italic',
                            'link',
                            'orderedList',
                            'bulletList',
                        ]),
                ])
                ->minItems(1)
                ->collapsible()
                ->cloneable()
                ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
        ];
    }
}