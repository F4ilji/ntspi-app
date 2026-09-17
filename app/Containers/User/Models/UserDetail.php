<?php

namespace App\Containers\User\Models;

use App\Ship\Models\Model;
use App\Ship\Traits\HasContainerFactory;

class UserDetail extends Model
{
    use HasContainerFactory;

    protected static string $factory = \Database\Factories\UserDetailFactory::class;

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

