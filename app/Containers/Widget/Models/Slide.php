<?php

namespace App\Containers\Widget\Models;

use App\Containers\Widget\Data\Factories\SlideFactory;
use App\Ship\Traits\HasContainerFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Slide extends Model
{
    use HasContainerFactory;

    protected static string $factory = \App\Containers\Widget\Data\Factories\SlideFactory::class;

    protected $guarded = false;

    protected $casts = [
        'settings' => 'array',
        'image' => 'array',
    ];

    public function slider(): BelongsTo
    {
        return $this->belongsTo(Slider::class);
    }

    public function slidable()
    {
        return $this->morphTo();
    }

    protected static function newFactory()
    {
        return SlideFactory::new();
    }
}
