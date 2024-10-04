<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Tags\HasTags;

class Event extends Model
{
    use HasFactory, HasTags;

    protected $guarded = false;

    protected $casts = [
        'content' => 'array',
    ];

    public function seo()
    {
        return $this->morphOne(Seo::class, 'seoable');
    }

    public function category() : BelongsTo
    {
        return $this->belongsTo(EventCategory::class);
    }
}
