<?php

namespace App\Containers\Dashboard\Actions\AdmissionCampaigns;

use App\Containers\Education\Models\AdmissionCampaign;
use App\Ship\Enums\Education\AdmissionCampaignStatus;

class ListAdmissionCampaignsAction
{
    public function run(array $filters = []): array
    {
        $query = AdmissionCampaign::query();

        // Фильтр по статусу
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Фильтр по учебному году
        if (!empty($filters['academic_year'])) {
            $query->where('academic_year', $filters['academic_year']);
        }

        // Поиск по названию
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('name', 'like', '%' . $search . '%');
        }

        $campaigns = $query->orderBy('academic_year', 'desc')->paginate(20)->withQueryString();

        return [
            'campaigns' => $campaigns,
            'filters' => $filters,
            'statuses' => array_map(fn($status) => [
                'value' => $status->value,
                'label' => $status->getLabel(),
                'color' => $status->getColor(),
            ], AdmissionCampaignStatus::cases()),
            'academicYears' => $this->generateAcademicYears(),
        ];
    }

    private function generateAcademicYears(): array
    {
        $currentYear = (int) date('Y') - 5;
        $yearsAhead = 10;
        $academicYears = [];

        for ($i = 0; $i < $yearsAhead; $i++) {
            $startYear = $currentYear + $i;
            $endYear = $startYear + 1;
            $academicYears[] = "{$startYear}/{$endYear}";
        }

        return $academicYears;
    }
}
