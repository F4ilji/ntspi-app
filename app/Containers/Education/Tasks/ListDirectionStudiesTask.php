<?php

namespace App\Containers\Education\Tasks;

use App\Containers\Education\Models\AdmissionCampaign;
use App\Containers\Education\Models\DirectionStudy;
use App\Ship\Enums\Education\BudgetEducation;
use App\Ship\Enums\Education\FormEducation;
use App\Ship\Enums\Education\LevelEducational;
use Illuminate\Support\Str;

class ListDirectionStudiesTask
{
    public function run(AdmissionCampaign $activeCampaign, array $filters)
    {
        $query = DirectionStudy::query()
            ->withAdmissionCampaignByYear($activeCampaign->academic_year)
            ->withActivePrograms();

        if (isset($filters['level'])) {
            $query->where('lvl_edu', LevelEducational::fromName($filters['level'])->value);
        }

        if (isset($filters['form'])) {
            $this->applyFormFilter($query, $filters['form']);
        }

        if (isset($filters['budget'])) {
            $this->applyBudgetFilter($query, $filters['budget']);
        }

        if (isset($filters['direction'])) {
            $slugs = $filters['direction'];
            if (is_array($slugs)) {
                $query->whereIn('slug', $slugs);
            }
        }

        return $query->get();
    }

    private function applyFormFilter($query, $form)
    {
        $formValue = Str::of(FormEducation::fromName($form)->value)->toString();

        $query->whereHas('programs.admission_plans', function ($query) use ($formValue) {
            $query->whereJsonContains('contests', ['form_education' => $formValue]);
        })
            ->with(['programs' => function ($query) use ($formValue) {
                $query->whereHas('admission_plans', function ($q) use ($formValue) {
                    $q->whereJsonContains('contests', ['form_education' => $formValue]);
                });
            }]);
    }

    private function applyBudgetFilter($query, $budget): void
    {
        $budgetValue = Str::of(BudgetEducation::fromName($budget)->value)->toString();

        $query->whereHas('programs.admission_plans', function ($query) use ($budgetValue) {
            $query->where('contests', 'like', '%"form_budget":"'.$budgetValue.'"%');
        })
            ->with(['programs' => function ($query) use ($budgetValue) {
                $query->whereHas('admission_plans', function ($query) use ($budgetValue) {
                    $query->where('contests', 'like', '%"form_budget":"'.$budgetValue.'"%');
                });
            }]);
    }
}
