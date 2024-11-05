<?php

namespace App\Http\Controllers;

use App\Enums\BudgetEducation;
use App\Enums\FormEducation;
use App\Enums\LevelEducational;
use App\Http\Resources\CampaignDegreeResource;
use App\Http\Resources\ClientNavigationResource;
use App\Http\Resources\DirectionStudyResource;
use App\Http\Resources\EducationalProgramFullResource;
use App\Http\Resources\MainSectionResource;
use App\Models\AdmissionCampaign;
use App\Models\AdmissionPlan;
use App\Models\CampaignDegree;
use App\Models\DirectionStudy;
use App\Models\EducationalProgram;
use App\Models\MainSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ClientProgramController extends Controller
{
    public function index(Request $request)
    {
        $activeCampaign = AdmissionCampaign::query()->where('status', 1)->first();

        $uniqueValues = EducationalProgram::distinct()->pluck('lvl_edu');
        $levelsEducational = $uniqueValues->mapWithKeys(function ($level) {
            return [$level->name => $level->getLabel()];
        });

        $direction_studies = DirectionStudy::query()
            ->withAdmissionCampaignByYear($activeCampaign->academic_year)
            ->withActivePrograms()
            ->get();

        $level = request()->input('level');
        $form = request()->input('form');
        $budget = request()->input('budget');

        $naprs = DirectionStudyResource::collection(
            DirectionStudy::query()
                ->withAdmissionCampaignByYear($activeCampaign->academic_year)
                ->withActivePrograms()
                ->when($level, function ($query) use ($level) {
                    $query->where('lvl_edu', LevelEducational::fromName($level)->value);
                })
                ->when($form, function ($query) use ($form) {
                    $this->applyFormFilter($query, $form);
                })
                ->when($budget, function ($query) use ($budget) {
                    $this->applyBudgetFilter($query, $budget);
                })
                ->when(request()->input('direction'), function ($query) {
                    $slugs = request()->input('direction');
                    if (is_array($slugs)) {
                        $query->whereIn('slug', $slugs);
                    }
                })
                ->get()
        );


        $campaignName = $this->getAdmissionCampaignName();
        $formsEducational = FormEducation::cases();
        $formsEducational = collect($formsEducational);
        $formsEdu = $formsEducational->mapWithKeys(function ($formEducational) {
            return [$formEducational->name => $formEducational->getLabel()];
        });
        $typesBudget = BudgetEducation::cases();
        $typesBudget = collect($typesBudget);
        $budgetEdu = $typesBudget->mapWithKeys(function ($typeBudget) {
            return [$typeBudget->name => $typeBudget->getLabel()];
        });

        $filters = [
            'level_filter' => [
                'type' => 'level',
                'value' => request()->input('level'),
                'param' => 'level'
            ],
            'budget_filter' => [
                'type' => 'budget',
                'value' => request()->input('budget'),
                'param' => 'budget'
            ],
            'formEdu_filter' => [
                'type' => 'form',
                'value' => request()->input('form'),
                'param' => 'form'
            ],
            'direction_filter' => [
                'type' => 'direction',
                'value' => request()->input('direction'),
                'param' => 'direction'
            ],
        ];

        return Inertia::render('Client/Programs/Index',
            compact(
                'naprs',
                'campaignName',
                'levelsEducational',
                'filters',
                'formsEdu',
                'budgetEdu',
                'direction_studies'
            ));
    }

    public function show(string $slug)
    {
        $program = new EducationalProgramFullResource(EducationalProgram::query()->where('slug', $slug)->with(['admission_plans', 'directionStudy'])->first());
        $formsEducational = BudgetEducation::cases();
        $formsEducational = collect($formsEducational);
        $formsEdu = $formsEducational->mapWithKeys(function ($formEducational) {
            return [$formEducational->value => $formEducational->getLabel()];
        });

        $seo = $this->seo;
        return Inertia::render('Client/Programs/Show', compact('program', 'formsEdu', 'seo'));
    }

    private function getAdmissionCampaignName() : string
    {
        $campaign = AdmissionCampaign::query()->where('status', 1)->first();
        return $campaign->name;
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

    private function applyBudgetFilter($query, $budget)
    {
        $budgetValue = Str::of(BudgetEducation::fromName($budget)->value)->toString();

        $query->whereHas('programs.admission_plans', function ($query) use ($budgetValue) {
            $query->whereJsonContains('contests', [['places' => ['form_budget' => $budgetValue]]]);
        })
            ->with(['programs' => function ($query) use ($budgetValue) {
                $query->whereHas('admission_plans', function ($query) use ($budgetValue) {
                    $query->whereJsonContains('contests', [['places' => ['form_budget' => $budgetValue]]]);
                });
            }]);
    }
//    public function bakalavriat()
//    {
//        $naprs = DirectionStudyResource::collection(
//            DirectionStudy::forBachelorLevel()
//                ->withActiveAdmissionCampaign()
//                ->withActivePrograms()
//                ->get()
//        );
//        $campaignName = $this->getAdmissionCampaignName();
//        return Inertia::render('Client/Programs/Index', compact('naprs', 'campaignName'));
//    }
//
//    public function spo()
//    {
//        $naprs = DirectionStudyResource::collection(
//            DirectionStudy::forMiddleLevel()
//                ->withActiveAdmissionCampaign()
//                ->withActivePrograms()
//                ->get()
//        );
//        $campaignName = $this->getAdmissionCampaignName();
//        return Inertia::render('Client/Programs/Index', compact('naprs', 'campaignName'));
//
//    }
//
//    public function magistratura()
//    {
//        $naprs = DirectionStudyResource::collection(
//            DirectionStudy::forMasterLevel()
//                ->withActiveAdmissionCampaign()
//                ->withActivePrograms()
//                ->get()
//        );
//        $campaignName = $this->getAdmissionCampaignName();
//        return Inertia::render('Client/Programs/Index', compact('naprs', 'campaignName'));
//
//    }
}
