<?php

namespace App\Http\Controllers;

use App\Enums\FormEducation;
use App\Http\Resources\ClientEducationalGroupResource;
use App\Http\Resources\ScheduleResource;
use App\Models\EducationalGroup;
use App\Models\Faculty;
use App\Models\Schedule;
use App\Services\App\Seo\SeoPageProvider;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientScheduleController extends Controller
{
    public function __construct(readonly SeoPageProvider $seoPageProvider){}

    public function index(Request $request)
    {
        $educationalGroups = ClientEducationalGroupResource::collection(EducationalGroup::query()
            ->has('schedules')
            ->when(request()->input('search'), function ($query, $search) {
                $query->whereRaw('LOWER(title) like ?', ["%".strtolower($search)."%"]);
            })
            ->when(request()->input('favorite'), function ($query, $favorite) {
                $query->whereHas('schedules', function ($query) use ($favorite) {
                    $query->whereIn('id', $favorite);
                });
            })
            ->when(request()->input('form'), function ($query, $form) {
                $query->where('education_form_id', FormEducation::fromName($form)->value);

            })

            ->with('schedules')
            ->with('faculty')
            ->orderBy('title')
            ->get());

        $schedulesByFaculty = $educationalGroups->groupBy(function ($group) {
            return $group->faculty->title;
        });

        $schedulesByFaculty = $schedulesByFaculty->toArray();



        $forms_education = [];
        foreach (FormEducation::cases() as $case) {
            $forms_education[$case->name] = $case->getLabel();
        }

        $filters = [
            'form_education_filter' => [
                'type' => 'form',
                'value' => request()->input('form'),
                'param' => 'form'
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

        // Возвращаем данные в представление
        return Inertia::render('Client/Schedules/Index', compact('educationalGroups', 'filters', 'forms_education', 'schedulesByFaculty', 'seo'));
    }

    public function show($id)
    {
        $schedule = Schedule::find($id);
        return Inertia::render('Client/Schedules/Show', compact('schedule'));
    }
}
