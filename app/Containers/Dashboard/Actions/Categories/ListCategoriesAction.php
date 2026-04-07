<?php

namespace App\Containers\Dashboard\Actions\Categories;

use App\Containers\Article\Models\Category;

class ListCategoriesAction
{
    public function run(array $filters = []): array
    {
        $query = Category::query();

        // Поиск по названию
        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }

        // Фильтр по статусу
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        $categories = $query->orderBy('title')->paginate(20)->withQueryString();

        return [
            'categories' => $categories,
            'filters' => $filters,
        ];
    }
}
