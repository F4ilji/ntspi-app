<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum BudgetEducation: int implements HasLabel, HasColor
{
    case budget_quantity_position = 1; // Очная форма обучения
    case non_budget_quantity_position = 2; // Заочная форма обучения

    public static function fromName(string $name): ?self {
        return match ($name) {
            'budget_quantity_position' => self::budget_quantity_position,
            'non_budget_quantity_position' => self::non_budget_quantity_position,
            default => null,
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::budget_quantity_position => 'Бюджетные места',
            self::non_budget_quantity_position => 'С оплатой обучения',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::budget_quantity_position => 'success', // Цвет для очной формы
            self::non_budget_quantity_position => 'warning', // Цвет для заочной формы
        };
    }
}