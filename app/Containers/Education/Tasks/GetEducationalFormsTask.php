<?php

namespace App\Containers\Education\Tasks;

use App\Containers\Education\Models\AdmissionPlan;
use App\Ship\Enums\Education\FormEducation;
use Illuminate\Support\Facades\Cache;

class GetEducationalFormsTask
{
    public function run()
    {
        $cacheKeyForms = 'education_forms_list';
        return Cache::remember($cacheKeyForms, now()->addDay(), function () {
            return AdmissionPlan::distinct()
                ->pluck('contests')
                ->flatten(1)
                ->filter(fn ($item) => isset($item['form_education']))
                ->pluck('form_education')
                ->unique()
                ->map(fn ($item) => FormEducation::tryFrom((int)$item))
                ->filter()
                ->mapWithKeys(fn ($form) => [$form->name => $form->getLabel()]);
        });
    }
}
