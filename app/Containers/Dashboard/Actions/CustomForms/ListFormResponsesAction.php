<?php

namespace App\Containers\Dashboard\Actions\CustomForms;

use App\Containers\Widget\Models\CustomForm;

class ListFormResponsesAction
{
    public function run(CustomForm $form, array $filters = []): array
    {
        $query = $form->responses()->with('form');

        // Фильтр по статусу просмотра
        if (isset($filters['checked']) && $filters['checked'] !== '') {
            $query->where('checked', (bool) $filters['checked']);
        }

        // Поиск по ID
        if (!empty($filters['search'])) {
            $query->where('id', 'like', '%' . $filters['search'] . '%');
        }

        $responses = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        // Динамические колонки на основе полей формы
        $columns = collect($form->columns ?? [])->map(function ($field) {
            return [
                'name' => $field['data']['name_field'] ?? '',
                'title' => $field['data']['title_field'] ?? '',
                'type' => $field['type'] ?? 'text',
                'options' => $this->extractOptions($field),
            ];
        })->filter(fn($col) => !empty($col['name']))->values()->toArray();

        return [
            'form' => $form,
            'responses' => $responses,
            'columns' => $columns,
            'filters' => $filters,
        ];
    }

    private function extractOptions(array $field): array
    {
        if (!in_array($field['type'], ['single_choice', 'multiple_choice'])) {
            return [];
        }

        return collect($field['data']['columns'] ?? [])
            ->mapWithKeys(fn($opt) => [$opt['name_field'] ?? '' => $opt['title_field'] ?? ''])
            ->toArray();
    }
}
