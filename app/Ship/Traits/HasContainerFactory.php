<?php

namespace App\Ship\Traits;

use Illuminate\Database\Eloquent\Factories\HasFactory;

trait HasContainerFactory
{
    use HasFactory;

    protected static function newFactory(): mixed
    {
        if (property_exists(static::class, 'factory')) {
            return static::$factory::new();
        }

        return parent::newFactory();
    }
}
