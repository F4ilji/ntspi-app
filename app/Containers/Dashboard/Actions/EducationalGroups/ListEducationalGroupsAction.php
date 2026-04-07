<?php

namespace App\Containers\Dashboard\Actions\EducationalGroups;

use App\Containers\Schedule\Models\EducationalGroup;
use App\Ship\Enums\Education\FormEducation;
use App\Containers\InstituteStructure\Models\Faculty;

class ListEducationalGroupsAction
{
    public function run(array $filters = []): array
    {
        $query = EducationalGroup::with(['faculty']);

        // Фильтр по факультету
        if (!empty($filters['faculty_id'])) {
            $query->where('faculty_id', $filters['faculty_id']);
        }

        // Фильтр по форме обучения
        if (!empty($filters['education_form_id'])) {
            $query->where('education_form_id', $filters['education_form_id']);
        }

        // Поиск по названию группы
        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }

        $groups = $query->orderBy('title')->paginate(20)->withQueryString();

        return [
            'groups' => $groups,
            'filters' => $filters,
            'faculties' => Faculty::orderBy('title')->get(['id', 'title']),
            'educationForms' => array_map(fn($form) => [
                'value' => $form->value,
                'label' => $form->getLabel(),
                'color' => $form->getColor(),
                'name' => $form->name,
            ], FormEducation::cases()),
        ];
    }
}
