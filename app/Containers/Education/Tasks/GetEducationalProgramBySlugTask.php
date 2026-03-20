<?php

namespace App\Containers\Education\Tasks;

use App\Containers\Education\Models\EducationalProgram;
use App\Ship\Enums\CacheKeys;
use App\Ship\Enums\Education\AdmissionCampaignStatus;
use App\Ship\Enums\Education\EducationalProgramStatus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;

class GetEducationalProgramBySlugTask
{
    public function run(string $slug)
    {
        $cacheKeyProgram = CacheKeys::EDUCATION_PROGRAM_PREFIX->value . md5($slug);

        return Cache::remember($cacheKeyProgram, now()->addHours(1), function () use ($slug) {
            return EducationalProgram::query()
                ->where('slug', $slug)
                ->where('status', EducationalProgramStatus::PUBLISHED)
                ->with([
                    'admission_plans' => function ($query) {
                        $query->whereHas('admissionCampaign', function (Builder $q) {
                            $q->where('status', AdmissionCampaignStatus::ACTIVE);
                        });
                    },
                    'admission_plans.admissionCampaign',
                    'directionStudy',
                    'seo'
                ])
                ->firstOrFail();
        });
    }
}