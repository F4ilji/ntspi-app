<?php

namespace App\Containers\Event\Models;

use App\Ship\Models\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventCategory extends Model
{
    use HasFactory;

    protected $guarded = false;

    public function events() : HasMany
    {
        return $this->hasMany(Event::class, 'category_id', 'id');
    }
}
