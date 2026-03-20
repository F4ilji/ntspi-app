<?php

namespace App\Filament\Components\Forms\ItemForm\Blocks;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;

class VideoBlock implements BlockSchema
{

    public static function schema(): array
    {
        return [
            TextInput::make('mime')
                ->label('Тип видео')
                ->readOnly()
                ->helperText('Определяется автоматически'),
            TextInput::make('title')
                ->label('Название видео')
                ->placeholder('Введите название видео')
                ->required()
                ->maxLength(255)
                ->autofocus()
                ->helperText('Это название будет отображаться перед видео'),
            FileUpload::make('path')
                ->label('Видеофайл')
                ->required()
                ->acceptedFileTypes([
                    'video/mp4',
                    'video/quicktime',
                    'video/x-msvideo',
                    'video/x-ms-wmv',
                    'video/avi',
                    'video/webm',
                    'video/ogg',
                    'video/3gpp',
                    'video/3gpp2',
                    'video/x-m4v',
                ])
                ->disk('public')
                ->directory('videos')
                ->helperText('Поддерживаются популярные видеоформаты (MP4, MOV, AVI и др.)')
                ->afterStateUpdated(fn (callable $set, $state) => $set('mime', $state?->getMimeType())),
        ];
    }
}