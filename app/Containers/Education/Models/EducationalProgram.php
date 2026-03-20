<?php

namespace App\Containers\Education\Models;

use App\Containers\InstituteStructure\Models\Department;
use App\Ship\Enums\Education\LevelEducational;
use App\Ship\Models\Model;
use App\Ship\Traits\HasSeo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EducationalProgram extends Model
{
    use HasFactory, HasSeo;

    protected $guarded = false;


    protected $casts = [
        'program_features' => 'array',
        'about_program' => 'array',
        'learning_forms' => 'array',
        'lvl_edu' => LevelEducational::class,
    ];

    public function directionStudy(): BelongsTo
    {
        return $this->belongsTo(DirectionStudy::class);
    }

    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'program_department');
    }


    public function admission_plans(): HasMany
    {
        return $this->hasMany(AdmissionPlan::class, 'educational_programs_id', 'id');
    }
}
