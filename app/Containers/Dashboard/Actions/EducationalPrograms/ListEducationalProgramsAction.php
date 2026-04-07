<?php

namespace App\Containers\Dashboard\Actions\EducationalPrograms;

use App\Containers\Education\Models\EducationalProgram;
use App\Containers\Education\Models\DirectionStudy;
use App\Ship\Enums\Education\EducationalProgramStatus;
use App\Ship\Enums\Education\LevelEducational;

class ListEducationalProgramsAction
{
    public function run(array $filters = []): array
    {
        $query = EducationalProgram::with(['directionStudy']);

        // Фильтр по уровню образования
        if (!empty($filters['lvl_edu'])) {
            $query->where('lvl_edu', $filters['lvl_edu']);
        }

        // Фильтр по статусу
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Фильтр по направлению подготовки
        if (!empty($filters['direction_study_id'])) {
            $query->where('direction_study_id', $filters['direction_study_id']);
        }

        // Поиск по названию
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('name', 'like', '%' . $search . '%');
        }

        $programs = $query->orderBy('name')->paginate(20)->withQueryString();

        return [
            'programs' => $programs,
            'filters' => $filters,
            'statuses' => array_map(fn($status) => [
                'value' => $status->value,
                'label' => $status->getLabel(),
                'color' => $status->getColor(),
            ], EducationalProgramStatus::cases()),
            'educationLevels' => array_map(fn($level) => [
                'value' => $level->value,
                'label' => $level->getLabel(),
                'color' => $level->getColor(),
            ], LevelEducational::cases()),
            'directionStudies' => DirectionStudy::orderBy('code')->get(['id', 'code', 'name']),
        ];
    }
}
