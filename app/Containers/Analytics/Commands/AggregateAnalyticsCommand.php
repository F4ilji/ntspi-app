<?php

namespace App\Containers\Analytics\Commands;

use App\Containers\Analytics\Models\AnalyticsDailyStat;
use App\Containers\Analytics\Models\AnalyticsHit;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;

class AggregateAnalyticsCommand extends \Illuminate\Console\Command
{
    protected $signature = 'analytics:aggregate {--days=1 : Days back to aggregate}';

    protected $description = 'Aggregate analytics hits into daily stats';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $date = now()->subDays($days)->toDateString();

        $stats = AnalyticsHit::selectRaw('
            DATE(created_at) as hit_date,
            url,
            COUNT(*) as views_count,
            COUNT(DISTINCT session_id) as unique_visitors
        ')
            ->whereDate('created_at', $date)
            ->groupBy(DB::raw('DATE(created_at)'), 'url')
            ->get();

        $inserted = 0;

        foreach ($stats as $row) {
            AnalyticsDailyStat::updateOrCreate(
                ['date' => $row->hit_date, 'url' => $row->url],
                [
                    'views_count' => $row->views_count,
                    'unique_visitors' => $row->unique_visitors,
                ]
            );
            $inserted++;
        }

        $this->info("Aggregated {$inserted} daily stat rows for {$date}");

        return static::SUCCESS;
    }
}
