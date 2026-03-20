<?php

namespace App\Containers\Schedule\Tasks;

use App\Ship\Builders\FilterBuilder;

class BuildScheduleFiltersTask
{
    public function __construct(
        private readonly FilterBuilder $filterBuilder
    ) {}

    public function run(array $requestFilters): array
    {
        // Сброс фильтров перед новым запуском
        $this->filterBuilder->reset();

        // 1. Поисковый фильтр
        $this->filterBuilder->add(
            key: 'search_filter',
            type: 'search',
            value: $requestFilters['search'] ?? null,
            param: 'search'
        );

        // 2. Фильтр по форме образования
        $this->filterBuilder->add(
            key: 'form_education_filter',
            type: 'form',
            value: $requestFilters['form'] ?? null,
            param: 'form'
        );

        // 3. Фильтр по факультету
        $this->filterBuilder->add(
            key: 'faculty_filter',
            type: 'faculty',
            value: $requestFilters['faculty'] ?? null,
            param: 'faculty'
        );

        // 4. Фильтр избранного
        $this->filterBuilder->add(
            key: 'favorite_filter',
            type: 'favorite',
            value: $requestFilters['favorite'] ?? null,
            param: 'favorite'
        );

        return $this->filterBuilder->get();
    }
}
