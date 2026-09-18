<?php

namespace App\Containers\Analytics\Tasks;

use App\Containers\Analytics\Models\AnalyticsDailyStat;
use App\Containers\Analytics\Models\AnalyticsHit;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GetDailyStatsTask
{
    public function run(int $days = 30): array
    {
        $from = Carbon::now()->subDays($days)->startOfDay();

        $stats = AnalyticsDailyStat::where('date', '>=', $from)
            ->selectRaw('
                date,
                SUM(views_count) as total_views,
                SUM(unique_visitors) as total_unique
            ')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        if ($stats->isEmpty()) {
            return AnalyticsHit::where('created_at', '>=', $from)
                ->selectRaw('
                    DATE(created_at) as hit_date,
                    COUNT(*) as total_views,
                    COUNT(DISTINCT session_id) as total_unique
                ')
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy('hit_date')
                ->get()
                ->map(fn($row) => [
                    'date' => (string) $row->hit_date,
                    'views' => (int) $row->total_views,
                    'visitors' => (int) $row->total_unique,
                ])
                ->toArray();
        }

        return $stats->map(fn($row) => [
            'date' => $row->date instanceof Carbon ? $row->date->format('Y-m-d') : (string) $row->date,
            'views' => (int) $row->total_views,
            'visitors' => (int) $row->total_unique,
        ])->toArray();
    }
}
