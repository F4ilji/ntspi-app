<?php

namespace App\Containers\Dashboard\Actions\ContactWidgets;

use App\Containers\Widget\Models\ContactWidget;

class ListContactWidgetsAction
{
    public function run(array $filters = []): array
    {
        $query = ContactWidget::query();

        // Поиск по названию
        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }

        // Фильтр по статусу
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        $widgets = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return [
            'widgets' => $widgets,
            'filters' => $filters,
        ];
    }
}
