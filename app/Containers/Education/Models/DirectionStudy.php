<?php

namespace App\Containers\Education\Models;

use App\Ship\Enums\Education\LevelEducational;
use App\Ship\Models\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;


class DirectionStudy extends Model
{
    use HasFactory;

    protected $guarded = false;

    protected $casts = [
        'lvl_edu' => LevelEducational::class,
    ];

    public function programs(): HasMany
    {
        return $this->hasMany(EducationalProgram::class);
    }

    public function scopeWithActivePrograms(Builder $query): Builder
    {
        return $query->with(['programs' => function ($query) {
            $query->whereHas('admission_plans.admissionCampaign', function ($q) {
                $q->where('status', 1);
            });
        }]);
    }

    public function scopeWithActiveAdmissionCampaign(Builder $query): Builder
    {
        return $query->whereHas('programs.admission_plans.admissionCampaign', function ($q) {
            $q->where('status', 1);
        });
    }

    public function scopeWithAdmissionCampaignByYear(Builder $query, string $year): Builder
    {
        return $query->whereHas('programs.admission_plans.admissionCampaign', function ($q) use ($year) {
            $q->where('status', 1)->where('academic_year', $year);
        });
    }

    public function scopeForBachelorLevel(Builder $query): Builder
    {
        return $query->where('lvl_edu', LevelEducational::BACHELOR);
    }

    public function scopeForMiddleLevel(Builder $query): Builder
    {
        return $query->whereIn('lvl_edu', [LevelEducational::MIDDLE_LEVEL_SPECIALIST_TRAINING, LevelEducational::PREPARATION_OF_QUALIFIED_WORKERS]);
    }

    public function scopeForMasterLevel(Builder $query): Builder
    {
        return $query->where('lvl_edu', LevelEducational::MASTER);
    }
}
