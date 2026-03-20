<?php

namespace App\Containers\InstituteStructure\Tasks;

use Illuminate\Database\Eloquent\Collection;

class GroupProgramsByDirectionTask
{
    public function run(Collection $programs): Collection
    {
        // Группируем программы по имени направления
        return $programs->groupBy(function ($program) {
            return $program->directionStudy->code . " " . $program->directionStudy->name; // Используем имя направления как ключ
        });
    }
}
