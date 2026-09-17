<?php

namespace App\Containers\Event\Models;

use App\Ship\Models\Model;
use App\Ship\Traits\HasContainerFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventCategory extends Model
{
    use HasContainerFactory;

    protected static string $factory = \Database\Factories\EventCategoryFactory::class;

    protected $guarded = false;

    public function events() : HasMany
    {
        return $this->hasMany(Event::class, 'category_id', 'id');
    }
}
