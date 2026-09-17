<?php

namespace App\Containers\Widget\Models;

use App\Ship\Traits\HasContainerFactory;
use Illuminate\Database\Eloquent\Model;

class ContactWidget extends Model
{
    use HasContainerFactory;

    protected static string $factory = \Database\Factories\ContactWidgetFactory::class;

    protected $guarded = false;

    protected $casts = [
        'content' => 'array',
    ];
}
