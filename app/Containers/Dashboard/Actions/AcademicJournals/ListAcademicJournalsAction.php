<?php

namespace App\Containers\Dashboard\Actions\AcademicJournals;

use App\Containers\Science\Models\AcademicJournal;

class ListAcademicJournalsAction
{
    public function run(array $filters = []): array
    {
        $query = AcademicJournal::query();

        // Поиск по названию
        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }

        $journals = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return [
            'journals' => $journals,
            'filters' => $filters,
        ];
    }
}
