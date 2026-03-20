<?php

namespace App\Containers\Widget\Models;

use App\Containers\Widget\Data\Factories\SlideFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Slide extends Model
{
    use HasFactory;

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
