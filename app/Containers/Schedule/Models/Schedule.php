<?php

namespace App\Containers\Schedule\Models;


use App\Ship\Models\Model;
use App\Ship\Traits\HasContainerFactory;

class Schedule extends Model
{
    use HasContainerFactory;

    protected static string $factory = \Database\Factories\ScheduleFactory::class;

    protected $guarded = false;

    protected $casts = [
        'file' => 'array',
    ];

    public function educationalGroup(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(EducationalGroup::class);
    }
}
