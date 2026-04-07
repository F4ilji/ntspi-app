<?php

namespace App\Containers\Dashboard\Actions\AdmissionPlans;

use App\Containers\Education\Models\AdmissionPlan;
use App\Containers\Education\Models\AdmissionCampaign;
use App\Containers\Education\Models\EducationalProgram;
use App\Ship\Enums\Education\EducationalProgramStatus;

class ListAdmissionPlansAction
{
    public function run(array $filters = []): array
    {
        $query = AdmissionPlan::with(['educationalProgram', 'admissionCampaign']);

        // Фильтр по приемной кампании
        if (!empty($filters['admission_campaigns_id'])) {
            $query->where('admission_campaigns_id', $filters['admission_campaigns_id']);
        }

        // Фильтр по образовательной программе
        if (!empty($filters['educational_programs_id'])) {
            $query->where('educational_programs_id', $filters['educational_programs_id']);
        }

        $plans = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();

        return [
            'plans' => $plans,
            'filters' => $filters,
            'admissionCampaigns' => AdmissionCampaign::orderBy('name')->get(['id', 'name', 'academic_year']),
            'educationalPrograms' => EducationalProgram::whereIn('status', [
                EducationalProgramStatus::PUBLISHED,
                EducationalProgramStatus::IN_PROGRESS
            ])->orderBy('name')->get(['id', 'name']),
        ];
    }
}
