<?php

namespace App\Containers\Main\Tasks;

use App\Containers\AdditionalEducation\Models\AdditionalEducation;
use App\Containers\AdditionalEducation\Models\AdditionalEducationCategory;
use App\Containers\Education\Models\AdmissionCampaign;
use App\Ship\Enums\Education\AdmissionCampaignStatus;
use App\Ship\Enums\Education\LevelEducational;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class GetEducationsDataTask
{
    public function run(): array
    {
        return Cache::remember('educations_data', now()->addHour(), function () {
            return $this->getEducationsData();
        });
    }

    private function getEducationsData(): array
    {
        return [
            'admission_campaign' => $this->getAdmissionCampaign(),
            'additional_education' => [
                'educations_count' => AdditionalEducation::where('is_active', true)->count(),
                'categories_count' => AdditionalEducationCategory::where('is_active', true)->count(),
            ],
        ];
    }

    private function getAdmissionCampaign(): array
    {
        $camp = AdmissionCampaign::where('status', AdmissionCampaignStatus::ACTIVE)->latest()->first();
        $info = $camp->info ?? [];

        return collect($info)->reduce(function ($carry, $a) {
            $lvl = LevelEducational::from((int)$a['edu_name'])->name;
            $carry[$lvl] = [
                'total_programs' => $a['total_programs'],
                'places' => [
                    'och_count' => $a['och_count'],
                    'zaoch_count' => $a['zaoch_count'],
                    'budget_places' => $a['budget_places'],
                    'non_budget_places' => $a['non_budget_places']
                ],
            ];
            return $carry;
        }, []);
    }
}