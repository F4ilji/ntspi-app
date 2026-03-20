<?php

namespace App\Containers\Schedule\Tasks;

use App\Containers\Schedule\Models\EducationalGroup;
use App\Ship\Exceptions\NotFoundException;
use App\Ship\Enums\Education\FormEducation;
use App\Containers\InstituteStructure\Models\Faculty;

class GetFilteredEducationalGroupsTask
{
    public function run(array $filters)
    {
        $educationalGroups = EducationalGroup::query()
            ->has('schedules')
            ->when(isset($filters['search']), function ($query) use ($filters) {
                $query->whereRaw('LOWER(title) like ?', ["%".strtolower($filters['search'])."%"]);
            })
            ->when(isset($filters['favorite']), function ($query) use ($filters) {
                $query->whereIn('id', $filters['favorite']);
            })
            ->when(isset($filters['form']), function ($query) use ($filters) {
                $query->where('education_form_id', FormEducation::fromName($filters['form'])->value);
            })
            ->when(isset($filters['faculty']), function ($query) use ($filters) {
                $query->whereHas('faculty', function ($q) use ($filters) {
                    $q->where('slug', $filters['faculty']);
                });
            })
            ->with('schedules')
            ->with('faculty')
            ->orderBy('faculty_id')
            ->orderBy('title');

        return $educationalGroups->paginate(10);
    }
}
