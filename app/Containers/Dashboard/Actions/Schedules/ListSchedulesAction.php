<?php

namespace App\Containers\Dashboard\Actions\Schedules;

use App\Containers\Schedule\Models\Schedule;
use App\Containers\Schedule\Models\EducationalGroup;
use App\Ship\Enums\Education\FormEducation;

class ListSchedulesAction
{
    public function run(array $filters = []): array
    {
        $query = Schedule::with(['educationalGroup.faculty']);

        // Фильтр по учебной группе
        if (!empty($filters['educational_group_id'])) {
            $query->where('educational_group_id', $filters['educational_group_id']);
        }

        // Фильтр по форме обучения (через таблицу educational_groups)
        if (!empty($filters['education_form_id'])) {
            $query->whereHas('educationalGroup', function ($q) use ($filters) {
                $q->where('education_form_id', $filters['education_form_id']);
            });
        }

        // Поиск по названию группы
        if (!empty($filters['search'])) {
            $query->whereHas('educationalGroup', function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%');
            });
        }

        $schedules = $query->orderByDesc('updated_at')->paginate(20)->withQueryString();

        return [
            'schedules' => $schedules,
            'filters' => $filters,
            'educationalGroups' => EducationalGroup::orderBy('title')->get(['id', 'title']),
            'educationForms' => array_map(fn($form) => [
                'value' => $form->value,
                'label' => $form->getLabel(),
                'color' => $form->getColor(),
                'name' => $form->name,
            ], FormEducation::cases()),
        ];
    }
}
