<?php

namespace App\Containers\Dashboard\Actions\AdditionalEducations;

use App\Containers\AdditionalEducation\Models\AdditionalEducation;
use App\Containers\AdditionalEducation\Models\AdditionalEducationCategory;
use App\Ship\Enums\Education\FormEducation;

class ListAdditionalEducationsAction
{
    public function run(array $filters = []): array
    {
        $query = AdditionalEducation::with(['category']);

        // Фильтр по категории
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Фильтр по форме обучения
        if (!empty($filters['form_education'])) {
            $query->where('form_education', $filters['form_education']);
        }

        // Фильтр по активности
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN));
        }

        // Поиск по названию или целевой аудитории
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('target_group', 'like', '%' . $search . '%');
            });
        }

        $educations = $query->orderBy('title')->paginate(20)->withQueryString();

        return [
            'educations' => $educations,
            'filters' => $filters,
            'categories' => AdditionalEducationCategory::where('is_active', true)
                ->orderBy('title')
                ->get(['id', 'title']),
            'educationForms' => array_map(fn($form) => [
                'value' => $form->value,
                'label' => $form->getLabel(),
                'color' => $form->getColor(),
                'name' => $form->name,
            ], FormEducation::cases()),
        ];
    }
}
