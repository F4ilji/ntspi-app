<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum FormEducation: int implements HasLabel, HasColor
{
    case FULL_TIME = 1; // Очная форма обучения
    case PART_TIME = 2; // Заочная форма обучения

    case FULL_PART_TIME = 3; // Заочная форма обучения


    public static function fromName(string $name): ?self {
        return match ($name) {
            'FULL_TIME' => self::FULL_TIME,
            'PART_TIME' => self::PART_TIME,
            'FULL_PART_TIME' => self::FULL_PART_TIME,
            default => null,
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::FULL_TIME => 'Очная форма обучения',
            self::PART_TIME => 'Заочная форма обучения',
            self::FULL_PART_TIME => 'Очно-заочная форма обучения'
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::FULL_TIME => 'success', // Цвет для очной формы
            self::PART_TIME => 'warning', // Цвет для заочной формы
            self::FULL_PART_TIME => 'info',
        };
    }
}