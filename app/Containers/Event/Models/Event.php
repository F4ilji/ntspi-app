<?php

namespace App\Containers\Event\Models;

use App\Ship\Models\Model;
use App\Ship\Traits\HasSeo;
use App\Ship\Traits\HasContainerFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Tags\HasTags;

class Event extends Model
{
    use HasContainerFactory, HasTags, HasSeo;

    protected static string $factory = \Database\Factories\EventFactory::class;

    protected $guarded = false;

    protected $casts = [
        'content' => 'array',
    ];


    public function category() : BelongsTo
    {
        return $this->belongsTo(EventCategory::class);
    }
}
