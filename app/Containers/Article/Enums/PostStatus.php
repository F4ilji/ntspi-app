<?php

namespace App\Containers\Article\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum PostStatus: string implements HasLabel, HasColor
{

    case DRAFT = 'draft';
    case VERIFICATION = 'verification';
    case PUBLISHED = 'published';
    case REJECTED = 'rejected';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::DRAFT => 'Черновик',
            self::VERIFICATION => 'На рассмотрении',
            self::PUBLISHED => 'Опубликовано',
            self::REJECTED => 'Отклонено',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::VERIFICATION => 'warning',
            self::PUBLISHED => 'success',
            self::REJECTED => 'gray',
        };
    }
}