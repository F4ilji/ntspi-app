<?php

namespace App\Filament\Components\Forms\ItemForm\Blocks;

use App\Enums\PostStatus;
use App\Models\Post;
use Filament\Forms\Components\Select;

class PostItemBlock implements BlockSchema
{

    public static function schema(): array
    {
        return [
            Select::make('post')
                ->label('Новость')
                ->options(Post::query()->where('status', PostStatus::PUBLISHED)->pluck('title', 'id'))
                ->searchable()
                ->required()
                ->helperText('Выберите опубликованную новость'),
        ];
    }
}