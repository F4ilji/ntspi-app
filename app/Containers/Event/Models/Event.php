<?php

namespace App\Containers\Event\Models;

use App\Ship\Models\Model;
use App\Ship\Traits\HasSeo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Tags\HasTags;

class Event extends Model
{
    use HasFactory, HasTags, HasSeo;

    protected $guarded = false;

    protected $casts = [
        'content' => 'array',
    ];


    public function category() : BelongsTo
    {
        return $this->belongsTo(EventCategory::class);
    }
}
