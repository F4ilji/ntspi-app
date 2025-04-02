<?php

namespace App\Http\Controllers;

use App\Enums\BudgetEducation;
use App\Enums\CacheKeys;
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
use App\Services\App\Seo\SeoPageProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ClientProgramController extends Controller
{
    public function __construct(readonly SeoPageProvider $seoPageProvider){}

    public function index(Request $request)
    {
        $cacheKey = CacheKeys::EDUCATION_PROGRAMS_PREFIX->value . md5(serialize($request->all()));

        $data = Cache::remember($cacheKey, now()->addHours(1), function () use ($request) {
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
                    ->with('programs.admission_plans')
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

            $seo = $this->seoPageProvider->getSeoForCurrentPage();


            return compact(
                'naprs',
                'campaignName',
                'levelsEducational',
                'filters',
                'formsEdu',
                'budgetEdu',
                'direction_studies',
                'seo'
            );
        });


        return Inertia::render('Client/Programs/Index', $data);
    }

    public function show(string $slug)
    {
        $cacheKey = CacheKeys::EDUCATION_PROGRAM_PREFIX->value . md5($slug);

        $data = Cache::remember($cacheKey, now()->addHours(1), function () use ($slug) {
            $program = new EducationalProgramFullResource(
                $programModel = EducationalProgram::query()
                    ->where('slug', $slug)
                    ->with(['admission_plans', 'directionStudy', 'seo'])
                    ->firstOrFail()
            );

            $formsEducational = BudgetEducation::cases();
            $formsEducational = collect($formsEducational);
            $formsEdu = $formsEducational->mapWithKeys(function ($formEducational) {
                return [$formEducational->value => $formEducational->getLabel()];
            });

            $seo = $this->seoPageProvider->getSeoForModel($programModel);

            return compact('program', 'formsEdu', 'seo');
        });

        return Inertia::render('Client/Programs/Show', $data);
    }

    private function getAdmissionCampaignName(): string
    {
        $cacheKey = CacheKeys::EDUCATION_PROGRAM_PREFIX->value . 'active_campaign_name';

        return Cache::remember($cacheKey, now()->addHours(1), function () {
            $campaign = AdmissionCampaign::query()->where('status', 1)->first();
            return $campaign->name;
        });
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
            $query->whereJsonContains('contests', ['financing_source' => $budgetValue]);
        })
            ->with(['programs' => function ($query) use ($budgetValue) {
                $query->whereHas('admission_plans', function ($query) use ($budgetValue) {
                    $query->whereJsonContains('contests', ['financing_source' => $budgetValue]);
                });
            }]);
    }
}