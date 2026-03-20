<?php

namespace App\Containers\Education\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum TypeExam: int implements HasLabel, HasColor
{
    case EGE = 1;
    case INTERNAL_EXAM = 2;
    case AVG_SCORE = 3;
    case ACCREDITATION = 4;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::EGE => 'ЕГЭ',
            self::INTERNAL_EXAM => 'ВИ, проводимое организацией самостоятельно',
            self::AVG_SCORE => 'Ср. балл документа об образовании',
            self::ACCREDITATION => 'Аккредитация',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::EGE => 'primary',
            self::INTERNAL_EXAM => 'info',
            self::AVG_SCORE => 'success',
            self::ACCREDITATION => 'warning',
        };
    }

    public static function fromName(string $name): ?self
    {
        return match ($name) {
            'EGE' => self::EGE,
            'INTERNAL_EXAM' => self::INTERNAL_EXAM,
            'AVG_SCORE' => self::AVG_SCORE,
            'ACCREDITATION' => self::ACCREDITATION,
            default => null,
        };
    }
}