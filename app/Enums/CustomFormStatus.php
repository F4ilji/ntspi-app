<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum CustomFormStatus: string implements HasLabel, HasColor
{

    case PUBLISHED = 'published';
    case HIDDEN = 'hidden';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PUBLISHED => 'Опубликовано',
            self::HIDDEN => 'Скрыто',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PUBLISHED => 'success',
            self::HIDDEN => 'gray',
        };
    }
}