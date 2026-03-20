<?php

namespace App\Containers\Education\Tasks;

use App\Ship\Builders\FilterBuilder;
use Illuminate\Support\Arr;

class BuildFiltersTask
{
    public function __construct(
        private readonly FilterBuilder $filterBuilder
    ) {}

    public function run(array $filters): array
    {
        // Сброс фильтров перед новым запуском
        $this->filterBuilder->reset();

        // Уровень образования
        $this->filterBuilder->add(
            key: 'level_filter',
            type: 'level',
            value: $filters['level'] ?? null,
            param: 'level'
        );

        // Форма обучения
        $this->filterBuilder->add(
            key: 'formEdu_filter',
            type: 'form',
            value: $filters['form'] ?? null,
            param: 'form'
        );

        // Бюджет
        $this->filterBuilder->add(
            key: 'budget_filter',
            type: 'budget',
            value: $filters['budget'] ?? null,
            param: 'budget'
        );

        // Направление
        $this->filterBuilder->add(
            key: 'direction_filter',
            type: 'direction',
            value: $filters['direction'] ?? null,
            param: 'direction'
        );

        return $this->filterBuilder->get();
    }
}
