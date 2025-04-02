<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum AdmissionCampaignStatus: int implements HasLabel, HasColor
{
    case ACTIVE = 1;
    case ARCHIVED = 2;
    case HIDDEN = 3;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ACTIVE => 'Активная',
            self::ARCHIVED => 'Архивная',
            self::HIDDEN => 'Скрытая',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::ARCHIVED => 'gray',
            self::HIDDEN => 'danger',
        };
    }
}