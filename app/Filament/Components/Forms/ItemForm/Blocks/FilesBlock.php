<?php

namespace App\Filament\Components\Forms\ItemForm\Blocks;

use App\Filament\Components\Forms\ItemForm\Blocks\BlockSchema;
use App\Ship\Helpers\ByteConverter;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class FilesBlock implements BlockSchema
{

    public static function schema(): array
    {
        return [
            Repeater::make('file')
                ->label('Файлы')
                ->helperText('Загрузите один или несколько файлов')
                ->schema([
                    Hidden::make('expansion')->required(),
                    Hidden::make('size')->required(),
                    TextInput::make('title')
                        ->label('Название файла')
                        ->placeholder('Введите название файла')
                        ->required()
                        ->maxLength(255)
                        ->autofocus()
                        ->helperText('Это название будет отображаться пользователям'),
                    FileUpload::make('path')
                        ->label('Файл')
                        ->required()
                        ->helperText('Поддерживаются PDF, Word, Excel, PowerPoint и ZIP файлы (макс. 500KB)')
                        ->getUploadedFileNameForStorageUsing(
                            fn (TemporaryUploadedFile $file): string =>
                            str(Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '-' . Carbon::now()->timestamp) . '.' . $file->getClientOriginalExtension())
                        )
                        ->acceptedFileTypes([
                            'application/pdf',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                            'application/zip'
                        ])
                        ->maxSize(512000)
                        ->disk('public')
                        ->directory('files')
                        ->downloadable()
                        ->afterStateUpdated(function ($set, $state) {
                            $set('expansion', $state?->getClientOriginalExtension());
                            $set('size', ByteConverter::bytesToHuman($state?->getSize()));
                        })
                        ->visibility('public')
                        ->preserveFilenames()
                ])
                ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                ->collapsible()
                ->cloneable()
                ->grid(2),
        ];
    }
}