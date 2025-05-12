<?php

namespace App\Containers\User\Models;

use App\Ship\Models\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserDetail extends Model
{
    use HasFactory;

    protected $guarded = false;

    protected $casts = [
        'is_only_worker' => 'boolean',
        'awards' => 'array',
        'education' => 'array',
        'workExperience' => 'array',
        'professDisciplines' => 'array',
        'professionalRetraining' => 'array',
        'professionalDevelopment' => 'array',
        'attendedConferences' => 'array',
        'participationScienceProjects' => 'array',
        'publications' => 'array',
        'other' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id');

    }

}

