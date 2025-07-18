<?php

namespace App\Containers\Schedule\Actions;

use App\Containers\Schedule\Tasks\GetFilteredEducationalGroupsTask;
use App\Containers\Schedule\Tasks\GetAllFormsEducationTask;
use App\Containers\Schedule\Tasks\GetAllActiveFacultiesTask;
use App\Containers\Schedule\Tasks\BuildScheduleFiltersTask;
use App\Containers\Schedule\UI\WEB\Transformers\EducationalGroupResource;
use App\Ship\Contracts\SeoServiceInterface;

class ListSchedulesAction
{
    public function __construct(
        private readonly GetFilteredEducationalGroupsTask $getFilteredEducationalGroupsTask,
        private readonly SeoServiceInterface $seoPageProvider,
        private readonly GetAllFormsEducationTask $getAllFormsEducationTask,
        private readonly GetAllActiveFacultiesTask $getAllActiveFacultiesTask,
        private readonly BuildScheduleFiltersTask $buildScheduleFiltersTask,
    ) {}

    public function run(array $filters): array
    {
        $educationalGroups = $this->getFilteredEducationalGroupsTask->run($filters);

        $schedulesPaginate = $educationalGroups->toArray();
        unset($schedulesPaginate['data']);
        $educationalGroups = EducationalGroupResource::collection($educationalGroups->items());

        $schedulesByFaculty = $educationalGroups->groupBy(function ($group) {
            return $group->faculty->title;
        });

        $schedulesByFaculty = $schedulesByFaculty->toArray();

        $forms_education = $this->getAllFormsEducationTask->run();

        $faculties = $this->getAllActiveFacultiesTask->run();

        $filtersData = $this->buildScheduleFiltersTask->run($filters);

        $seo = $this->seoPageProvider->getSeoForCurrentPage();

        return [
            'filters' => $filtersData,
            'forms_education' => $forms_education,
            'schedulesByFaculty' => inertia()->deepMerge(fn() => $schedulesByFaculty),
            'schedules_paginator' => $schedulesPaginate,
            'seo' => $seo,
            'faculties' => $faculties,
        ];
    }
}
