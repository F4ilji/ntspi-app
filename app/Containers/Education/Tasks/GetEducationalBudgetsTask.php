<?php

namespace App\Containers\Education\Tasks;

use App\Containers\Education\Models\AdmissionPlan;
use App\Ship\Enums\Education\BudgetEducation;
use Illuminate\Support\Facades\Cache;

class GetEducationalBudgetsTask
{
    public function run()
    {
        $cacheKeyBudgets = 'education_budgets_list';
        return Cache::remember($cacheKeyBudgets, now()->addDay(), function () {
            return AdmissionPlan::distinct()
                ->pluck('contests')
                ->flatten(1)
                ->filter(fn ($item) => isset($item['places']['form_budget']))
                ->pluck('places.form_budget')
                ->unique()
                ->map(fn ($item) => BudgetEducation::tryFrom($item))
                ->filter()
                ->mapWithKeys(fn ($form) => [$form->name => $form->getLabel()]);
        });
    }
}
