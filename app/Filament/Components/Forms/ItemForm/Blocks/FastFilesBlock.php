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

class FastFilesBlock implements BlockSchema
{
    public static function schema(): array
    {
        return [
            FileUpload::make('path')
                ->label('Файл')
                ->required()
                ->multiple()
                ->helperText('Поддерживаются PDF, Word, Excel, PowerPoint и ZIP файлы')
                ->getUploadedFileNameForStorageUsing(
                    fn (TemporaryUploadedFile $file): string =>
                        // Генерируем уникальное имя файла для хранения, используя slug из оригинального имени и временную метку
                    str(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '-' . Carbon::now()->format('d-m-Y') . '.' . $file->getClientOriginalExtension())
                )
                ->acceptedFileTypes([
                    'application/pdf',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    'application/zip'
                ])
//                ->maxSize(512000) // 512MB
                ->disk('public')
                ->directory('files')
                ->downloadable()

                ->visibility('public'),
        ];
    }
}