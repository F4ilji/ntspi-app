<?php

namespace App\Containers\Schedule\UI\WEB\Controllers;

use App\Containers\InstituteStructure\Models\Faculty;
use App\Containers\Schedule\Models\EducationalGroup;
use App\Containers\Schedule\UI\WEB\Transformers\EducationalGroupResource;
use App\Ship\Contracts\SeoServiceInterface;
use App\Ship\Controllers\Controller;
use App\Ship\Enums\Education\FormEducation;
use Illuminate\Http\Request;

class ClientScheduleController extends Controller
{
    public function __construct(readonly SeoServiceInterface $seoPageProvider){}

    public function index(Request $request)
    {
        $educationalGroups = EducationalGroup::query()
            ->has('schedules')
            ->when(request()->input('search'), function ($query, $search) {
                $query->whereRaw('LOWER(title) like ?', ["%".strtolower($search)."%"]);
            })
            ->when(request()->input('favorite'), function ($query, $favorite) {
                $query->whereIn('id', $favorite);
            })
            ->when(request()->input('form'), function ($query, $form) {
                $query->where('education_form_id', FormEducation::fromName($form)->value);
            })
            ->when(request()->input('faculty'), function ($query, $facultySlug) {
                $query->whereHas('faculty', function ($q) use ($facultySlug) {
                    $q->where('slug', $facultySlug);
                });
            })
            ->with('schedules')
            ->with('faculty')
            ->orderBy('faculty_id')
            ->orderBy('title')
            ->paginate(10);

        $schedulesPaginate = $educationalGroups->toArray();
        unset($schedulesPaginate['data']);
        $educationalGroups = EducationalGroupResource::collection($educationalGroups->items());


        $schedulesByFaculty = $educationalGroups->groupBy(function ($group) {
            return $group->faculty->title;
        });

        $schedulesByFaculty = $schedulesByFaculty->toArray();

        $forms_education = [];
        foreach (FormEducation::cases() as $case) {
            $forms_education[$case->name] = $case->getLabel();
        }

        $faculties = Faculty::query()->where('is_active', true)->get();

        $filters = [
            'form_education_filter' => [
                'type' => 'form',
                'value' => request()->input('form'),
                'param' => 'form'
            ],
            'faculty_filter' => [
                'type' => 'faculty',
                'value' => request()->input('faculty'),
                'param' => 'faculty'
            ],
            'search_filter' => [
                'type' => 'search',
                'value' => $request->input('search'),
                'param' => 'search'
            ],
            'favorite_filter' => [
                'type' => 'favorite',
                'value' => $request->input('favorite'),
                'param' => 'favorite'
            ]
        ];

        $seo = $this->seoPageProvider->getSeoForCurrentPage();

        return inertia()->render(
            'Client/Schedules/Index',
            [
                'filters' => $filters,
                'forms_education' => $forms_education,
                'schedulesByFaculty' => inertia()->deepMerge(fn() => $schedulesByFaculty),
                'schedules_paginator' => $schedulesPaginate,
                'seo' => $seo,
                'faculties' => $faculties,
            ]
        );

        // Возвращаем данные в представление
    }

//    public function show($id)
//    {
//        $schedule = Schedule::find($id);
//        return Inertia::render('Client/Schedules/Show', compact('schedule'));
//    }
}
