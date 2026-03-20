<?php

namespace App\Containers\Education\Actions;

use App\Containers\Education\Models\AdmissionCampaign;
use App\Containers\Education\Models\AdmissionPlan;
use App\Containers\Education\Models\EducationalProgram;
use App\Containers\Education\UI\WEB\Transformers\DirectionStudyResource;
use App\Ship\Contracts\SeoServiceInterface;
use App\Ship\Enums\CacheKeys;
use App\Ship\Enums\Education\BudgetEducation;
use App\Ship\Enums\Education\FormEducation;
use App\Ship\Enums\Education\LevelEducational;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Containers\Education\Tasks\GetActiveAdmissionCampaignTask;
use App\Containers\Education\Tasks\GetEducationalLevelsTask;
use App\Containers\Education\Tasks\GetEducationalFormsTask;
use App\Containers\Education\Tasks\GetEducationalBudgetsTask;
use App\Containers\Education\Tasks\GetSeoForCurrentPageTask;
use App\Containers\Education\Tasks\ListDirectionStudiesTask;
use App\Containers\Education\Tasks\GetAdmissionCampaignNameTask;
use App\Containers\Education\Tasks\BuildFiltersTask;
use App\Containers\Education\Models\DirectionStudy;

class ListEducationalProgramsAction
{
    public function __construct(
        private readonly SeoServiceInterface $seoPageProvider,
        private readonly GetActiveAdmissionCampaignTask $getActiveAdmissionCampaignTask,
        private readonly GetEducationalLevelsTask $getEducationalLevelsTask,
        private readonly GetEducationalFormsTask $getEducationalFormsTask,
        private readonly GetEducationalBudgetsTask $getEducationalBudgetsTask,
        private readonly GetSeoForCurrentPageTask $getSeoForCurrentPageTask,
        private readonly ListDirectionStudiesTask $listDirectionStudiesTask,
        private readonly GetAdmissionCampaignNameTask $getAdmissionCampaignNameTask,
        private readonly BuildFiltersTask $buildFiltersTask,
    ) {}

    public function run(array $filters)
    {
        $request = Request::capture(); // This is a temporary solution, will refine later

        $cacheKey = CacheKeys::EDUCATION_PROGRAMS_PREFIX->value . md5(serialize($filters));

        $activeCampaign = $this->getActiveAdmissionCampaignTask->run();
        $levelsEducational = $this->getEducationalLevelsTask->run();
        $formsEdu = $this->getEducationalFormsTask->run();
        $budgetEdu = $this->getEducationalBudgetsTask->run();
        $seo = $this->getSeoForCurrentPageTask->run($this->seoPageProvider);
        $filtersData = $this->buildFiltersTask->run($filters);

        $naprs = Cache::remember($cacheKey, now()->addHours(), function () use ($request, $activeCampaign, $filters) {
            return DirectionStudyResource::collection(
                $this->listDirectionStudiesTask->run($activeCampaign, $filters)
            );
        });

        $data = [
            'naprs' => $naprs,
            'campaignName' => $this->getAdmissionCampaignNameTask->run(),
            'levelsEducational' => $levelsEducational,
            'filters' => $filtersData,
            'formsEdu' => $formsEdu,
            'budgetEdu' => $budgetEdu,
            'direction_studies' => DirectionStudy::query()
                ->withAdmissionCampaignByYear($activeCampaign->academic_year)
                ->withActivePrograms()
                ->get(),
            'seo' => $seo
        ];

        return $data;
    }
}
