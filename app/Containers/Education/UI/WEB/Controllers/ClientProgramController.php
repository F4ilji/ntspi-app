<?php

namespace App\Containers\Education\UI\WEB\Controllers;

use App\Containers\Education\Models\AdmissionCampaign;
use App\Containers\Education\Models\AdmissionPlan;
use App\Containers\Education\Models\DirectionStudy;
use App\Containers\Education\Models\EducationalProgram;
use App\Containers\Education\UI\WEB\Transformers\DirectionStudyResource;
use App\Containers\Education\UI\WEB\Transformers\EducationalProgramResource;
use App\Ship\Contracts\SeoServiceInterface;
use App\Ship\Controllers\Controller;
use App\Ship\Enums\CacheKeys;
use App\Ship\Enums\Education\BudgetEducation;
use App\Ship\Enums\Education\FormEducation;
use App\Ship\Enums\Education\LevelEducational;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ClientProgramController extends Controller
{
    public function __construct(readonly SeoServiceInterface $seoPageProvider){}

    public function index(Request $request): \Inertia\Response
    {
        $cacheKey = CacheKeys::EDUCATION_PROGRAMS_PREFIX->value . md5(serialize($request->all()));
        $cacheKeyLevels = 'education_levels_list';
        $cacheKeyForms = 'education_forms_list';
        $cacheKeyBudgets = 'education_budgets_list';
        $cacheKeySeo = 'education_programs_seo';

        $activeCampaign = Cache::remember(CacheKeys::ADMISSION_CAMPAIGNS_PREFIX->value, now()->addDay(), function () {
            return AdmissionCampaign::where('status', 1)->first();
        });

        $levelsEducational = Cache::remember($cacheKeyLevels, now()->addDay(), function () {
            return EducationalProgram::distinct()->pluck('lvl_edu')
                ->mapWithKeys(fn($level) => [$level->name => $level->getLabel()]);
        });

        $formsEdu = Cache::remember($cacheKeyForms, now()->addDay(), function () {
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


        $budgetEdu = Cache::remember($cacheKeyBudgets, now()->addDay(), function () {
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

        $seo = Cache::remember($cacheKeySeo, now()->addDay(), function () {
            return $this->seoPageProvider->getSeoForCurrentPage();
        });

        $naprs = Cache::remember($cacheKey, now()->addHours(), function () use ($request, $activeCampaign) {
            return DirectionStudyResource::collection(
                DirectionStudy::query()
                    ->withAdmissionCampaignByYear($activeCampaign->academic_year)
                    ->withActivePrograms()
//                    ->with('programs.admission_plans')
                    ->when($request->input('level'), fn($q, $level) =>
                    $q->where('lvl_edu', LevelEducational::fromName($level)->value))
                    ->when($request->input('form'), fn($q, $form) =>
                    $this->applyFormFilter($q, $form))
                    ->when($request->input('budget'), fn($q, $budget) =>
                    $this->applyBudgetFilter($q, $budget))
                    ->when($request->input('direction'), fn($q, $slugs) =>
                        is_array($slugs) ? $q->whereIn('slug', $slugs) : $q)
                    ->get()
            );
        });


        $data = [
            'naprs' => $naprs,
            'campaignName' => $this->getAdmissionCampaignName(),
            'levelsEducational' => $levelsEducational,
            'filters' => [
                'level_filter' => ['type' => 'level', 'value' => $request->input('level'), 'param' => 'level'],
                'budget_filter' => ['type' => 'budget', 'value' => $request->input('budget'), 'param' => 'budget'],
                'formEdu_filter' => ['type' => 'form', 'value' => $request->input('form'), 'param' => 'form'],
                'direction_filter' => ['type' => 'direction', 'value' => $request->input('direction'), 'param' => 'direction'],
            ],
            'formsEdu' => $formsEdu,
            'budgetEdu' => $budgetEdu,
            'direction_studies' => DirectionStudy::query()
                ->withAdmissionCampaignByYear($activeCampaign->academic_year)
                ->withActivePrograms()
                ->get(),
            'seo' => $seo
        ];

        return Inertia::render('Client/Programs/Index', $data);
    }
    public function show(string $slug): \Inertia\Response
    {
        $cacheKeyProgram = CacheKeys::EDUCATION_PROGRAM_PREFIX->value . md5($slug);
        $cacheKeySeo = CacheKeys::EDUCATION_PROGRAM_PREFIX->value . 'seo_' . md5($slug);
        $cacheKeyForms = 'education_forms_list';

        $programModel = Cache::remember($cacheKeyProgram, now()->addHours(1), function () use ($slug) {
            return EducationalProgram::query()
                ->where('slug', $slug)
                ->with(['admission_plans', 'directionStudy', 'seo'])
                ->firstOrFail();
        });

        $formsEdu = Cache::remember($cacheKeyForms, now()->addDay(), function () {
            return collect(BudgetEducation::cases())
                ->mapWithKeys(fn($form) => [$form->value => $form->getLabel()]);
        });

        $seo = Cache::remember($cacheKeySeo, now()->addHours(1), function () use ($programModel) {
            return $this->seoPageProvider->getSeoForModel($programModel);
        });

        $program = new EducationalProgramResource($programModel);

        return Inertia::render('Client/Programs/Show', compact('program', 'formsEdu', 'seo'));
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