<?php

namespace App\Containers\Article\Models;

use App\Ship\Models\Model;
use App\Ship\Traits\HasContainerFactory;

class Tag extends Model
{
    use HasContainerFactory;

    protected static string $factory = \Database\Factories\TagFactory::class;

    protected $casts = [
        'name' => 'array',
        'slug' => 'array',
    ];
}
