<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum BudgetEducation: int implements HasLabel, HasColor
{
    case MAIN_QUOTA = 1;
    case TARGET_QUOTA = 2;
    case SPECIAL_QUOTA = 3;
    case PAID_EDUCATION = 4;
    case OTHER_SOURCES = 5;
    case SEPARATE_QUOTA = 6;
    case COMBINED_QUOTA_ALL = 7;
    case COMBINED_QUOTA_TARGET_SPECIAL = 8;
    case COMBINED_QUOTA_TARGET_SEPARATE = 9;
    case COMBINED_QUOTA_SPECIAL_SEPARATE = 10;
    case DETAILED_TARGET_QUOTA = 11;

    public static function fromName(string $name): ?self {
        return match ($name) {
            'MAIN_QUOTA' => self::MAIN_QUOTA,
            'TARGET_QUOTA' => self::TARGET_QUOTA,
            'SPECIAL_QUOTA' => self::SPECIAL_QUOTA,
            'PAID_EDUCATION' => self::PAID_EDUCATION,
            'OTHER_SOURCES' => self::OTHER_SOURCES,
            'SEPARATE_QUOTA' => self::SEPARATE_QUOTA,
            'COMBINED_QUOTA_ALL' => self::COMBINED_QUOTA_ALL,
            'COMBINED_QUOTA_TARGET_SPECIAL' => self::COMBINED_QUOTA_TARGET_SPECIAL,
            'COMBINED_QUOTA_TARGET_SEPARATE' => self::COMBINED_QUOTA_TARGET_SEPARATE,
            'COMBINED_QUOTA_SPECIAL_SEPARATE' => self::COMBINED_QUOTA_SPECIAL_SEPARATE,
            'DETAILED_TARGET_QUOTA' => self::DETAILED_TARGET_QUOTA,
            default => null,
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::MAIN_QUOTA => 'Основные места',
            self::TARGET_QUOTA => 'Целевая квота',
            self::SPECIAL_QUOTA => 'Особая квота',
            self::PAID_EDUCATION => 'С оплатой обучения',
            self::OTHER_SOURCES => 'За счёт иных средств',
            self::SEPARATE_QUOTA => 'Отдельная квота',
            self::COMBINED_QUOTA_ALL => 'Совмещенная квота (целевая, особая и отдельная квоты)',
            self::COMBINED_QUOTA_TARGET_SPECIAL => 'Совмещенная квота (целевая и особая квоты)',
            self::COMBINED_QUOTA_TARGET_SEPARATE => 'Совмещенная квота (целевая и отдельная квоты)',
            self::COMBINED_QUOTA_SPECIAL_SEPARATE => 'Совмещенная квота (особая и отдельная квоты)',
            self::DETAILED_TARGET_QUOTA => 'Детализированная целевая квота',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::MAIN_QUOTA => 'primary',
            self::TARGET_QUOTA => 'info',
            self::SPECIAL_QUOTA => 'danger',
            self::PAID_EDUCATION => 'warning',
            self::OTHER_SOURCES => 'gray',
            self::SEPARATE_QUOTA => 'success',
            self::COMBINED_QUOTA_ALL => 'indigo',
            self::COMBINED_QUOTA_TARGET_SPECIAL => 'violet',
            self::COMBINED_QUOTA_TARGET_SEPARATE => 'fuchsia',
            self::COMBINED_QUOTA_SPECIAL_SEPARATE => 'pink',
            self::DETAILED_TARGET_QUOTA => 'amber',
        };
    }
}