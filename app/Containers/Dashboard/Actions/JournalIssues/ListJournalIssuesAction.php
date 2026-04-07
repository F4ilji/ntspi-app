<?php

namespace App\Containers\Dashboard\Actions\JournalIssues;

use App\Containers\Science\Models\JournalIssue;

class ListJournalIssuesAction
{
    public function run(int $journalId, array $filters = []): array
    {
        $query = JournalIssue::where('academic_journal_id', $journalId);

        // Фильтр по году
        if (!empty($filters['year_publication'])) {
            $query->where('year_publication', $filters['year_publication']);
        }

        // Фильтр по статусу
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        // Поиск по названию
        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }

        $issues = $query->orderBy('sort')->orderBy('year_publication', 'desc')->paginate(20)->withQueryString();

        // Получаем уникальные годы для фильтра (оптимизировано через groupBy)
        $years = JournalIssue::where('academic_journal_id', $journalId)
            ->groupBy('year_publication')
            ->orderBy('year_publication', 'desc')
            ->pluck('year_publication');

        return [
            'issues' => $issues,
            'filters' => $filters,
            'years' => $years,
        ];
    }
}
