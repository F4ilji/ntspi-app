<?php

namespace App\Containers\Analytics\Tasks;

use App\Containers\Analytics\Models\AnalyticsHit;
use App\Containers\Analytics\Models\AnalyticsSession;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GetAnalyticsOverviewTask
{
    public function run(int $days = 30): array
    {
        $from = Carbon::now()->subDays($days)->startOfDay();
        $to = now()->endOfDay();

        if ($days === 0) {
            $prevFrom = Carbon::yesterday()->startOfDay();
            $prevTo = Carbon::yesterday()->endOfDay();
        } else {
            $prevFrom = Carbon::now()->subDays($days * 2)->startOfDay();
            $prevTo = Carbon::now()->subDays($days)->startOfDay();
        }

        $current = $this->getPeriodStats($from, $to);
        $previous = $this->getPeriodStats($prevFrom, $prevTo);

        return [
            'unique_visitors' => $this->compare($current['visitors'], $previous['visitors']),
            'page_views' => $this->compare($current['views'], $previous['views']),
            'avg_time' => $this->compare($current['avg_time'], $previous['avg_time'], true),
            'bounce_rate' => $this->compare($current['bounce_rate'], $previous['bounce_rate'], true, true),
        ];
    }

    private function getPeriodStats(Carbon $from, Carbon $to): array
    {
        $hits = AnalyticsHit::whereBetween('created_at', [$from, $to])->count();

        $sessions = AnalyticsSession::whereBetween('started_at', [$from, $to])
            ->get(['id', 'started_at', 'last_activity_at']);

        $uniqueVisitors = AnalyticsSession::whereBetween('started_at', [$from, $to])
            ->distinct('visitor_id')
            ->count('visitor_id');

        $totalSessions = $sessions->count();

        $avgTime = 0;
        if ($totalSessions > 0) {
            $totalSeconds = $sessions->sum(function ($s) {
                return $s->started_at->diffInSeconds($s->last_activity_at);
            });
            $avgTime = (int) round($totalSeconds / $totalSessions);
        }

        $bounceRate = 0;
        if ($totalSessions > 0) {
            $sessionIds = $sessions->pluck('id');
            $singleHitSessions = AnalyticsHit::whereIn('session_id', $sessionIds)
                ->selectRaw('session_id, COUNT(*) as hits')
                ->groupBy('session_id')
                ->havingRaw('COUNT(*) = 1')
                ->count();
            $bounceRate = (int) round(($singleHitSessions / $totalSessions) * 100);
        }

        return [
            'views' => $hits,
            'visitors' => $uniqueVisitors,
            'avg_time' => $avgTime,
            'bounce_rate' => $bounceRate,
        ];
    }

    private function compare(int $current, int $previous, bool $isRatio = false, bool $invertDirection = false): array
    {
        $change = 0;
        if ($previous > 0) {
            $change = $isRatio
                ? $current - $previous
                : (int) round((($current - $previous) / $previous) * 100);
        } elseif ($current > 0) {
            $change = $isRatio ? 0 : 100;
        }

        $direction = $invertDirection
            ? ($change < 0 ? 'up' : ($change > 0 ? 'down' : 'neutral'))
            : ($change > 0 ? 'up' : ($change < 0 ? 'down' : 'neutral'));

        return [
            'value' => $current,
            'change' => $change,
            'direction' => $direction,
        ];
    }
}
