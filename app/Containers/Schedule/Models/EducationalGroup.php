<?php

namespace App\Containers\Schedule\Models;

use App\Containers\InstituteStructure\Models\Faculty;
use App\Ship\Models\Model;
use App\Ship\Traits\HasContainerFactory;

class EducationalGroup extends Model
{
    use HasContainerFactory;

    protected static string $factory = \Database\Factories\EducationalGroupFactory::class;

    protected $guarded = false;

    public function faculty(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }
    public function schedules(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Schedule::class);
    }
}
