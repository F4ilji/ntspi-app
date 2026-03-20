<?php

namespace App\Containers\Schedule\Models;


use App\Ship\Models\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Schedule extends Model
{
    use HasFactory;

    protected $guarded = false;

    protected $casts = [
        'file' => 'array',
    ];

    public function educationalGroup(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(EducationalGroup::class);
    }
}
