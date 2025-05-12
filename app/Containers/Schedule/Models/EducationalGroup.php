<?php

namespace App\Containers\Schedule\Models;

use App\Containers\InstituteStructure\Models\Faculty;
use App\Ship\Models\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EducationalGroup extends Model
{
    use HasFactory;

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
