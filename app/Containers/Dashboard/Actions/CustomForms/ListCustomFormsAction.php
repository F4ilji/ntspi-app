<?php

namespace App\Containers\Dashboard\Actions\CustomForms;

use App\Containers\Widget\Models\CustomForm;

class ListCustomFormsAction
{
    public function run(array $filters = []): array
    {
        $query = CustomForm::query();

        // Поиск по названию
        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }

        // Фильтр по статусу
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $forms = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return [
            'forms' => $forms,
            'filters' => $filters,
            'statuses' => [
                ['value' => 'published', 'label' => 'Опубликовано'],
                ['value' => 'hidden', 'label' => 'Скрыто'],
            ],
        ];
    }
}
