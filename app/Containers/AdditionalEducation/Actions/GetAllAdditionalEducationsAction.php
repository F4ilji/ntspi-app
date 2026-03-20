<?php

namespace App\Containers\AdditionalEducation\Actions;

use App\Containers\AdditionalEducation\Tasks\GetAllAdditionalEducationCategoriesPreviewTask;
use App\Containers\AdditionalEducation\Tasks\GetDirectionAdditionalEducationsTask;
use App\Containers\AdditionalEducation\Tasks\GetFilteredAdditionalEducationCategoriesTask;
use App\Containers\AdditionalEducation\Tasks\BuildFormsEducationArrayTask;
use App\Containers\AdditionalEducation\Tasks\BuildAdditionalEducationFiltersTask;
use App\Ship\Actions\Action;
use App\Ship\Contracts\SeoServiceInterface;
use Illuminate\Http\Request;

class GetAllAdditionalEducationsAction
{
    public function __construct(
        readonly SeoServiceInterface $seoPageProvider,
        readonly GetDirectionAdditionalEducationsTask $getDirectionAdditionalEducationsTask,
        readonly GetFilteredAdditionalEducationCategoriesTask $getFilteredAdditionalEducationCategoriesTask,
        readonly GetAllAdditionalEducationCategoriesPreviewTask $getAllAdditionalEducationCategoriesPreviewTask,
        readonly BuildFormsEducationArrayTask $buildFormsEducationArrayTask,
        readonly BuildAdditionalEducationFiltersTask $buildAdditionalEducationFiltersTask,
    ) {}

    public function run(Request $request): array
    {
        $directionAdditionalEducations = $this->getDirectionAdditionalEducationsTask->run($request);
        $additionalEducations = $this->getFilteredAdditionalEducationCategoriesTask->run($request);
        $categories = $this->getAllAdditionalEducationCategoriesPreviewTask->run();
        $forms_education = $this->buildFormsEducationArrayTask->run();
        $seo = $this->seoPageProvider->getSeoForCurrentPage();
        $filters = $this->buildAdditionalEducationFiltersTask->run($request);

        return compact(
            'directionAdditionalEducations',
            'additionalEducations',
            'filters',
            'forms_education',
            'categories',
            'seo'
        );
    }
}
