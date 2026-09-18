<?php

namespace App\Containers\Analytics\Tasks;

use App\Containers\Analytics\Models\AnalyticsHit;
use Illuminate\Support\Facades\DB;

class GetTopPagesTask
{
    public function run(int $days = 7, int $limit = 10): array
    {
        return AnalyticsHit::select('url')
            ->selectRaw('COUNT(*) as views')
            ->selectRaw('COUNT(DISTINCT session_id) as unique_visitors')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('url')
            ->orderByDesc('views')
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
