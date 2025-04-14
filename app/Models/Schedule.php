<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $guarded = false;

    protected $casts = [
        'file' => 'array',
    ];

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function educationalGroup(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(EducationalGroup::class);
    }


}
