<?php

namespace App\Containers\Analytics\Tasks;

use App\Containers\Analytics\Models\AnalyticsHit;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GetReferrersTask
{
    public function run(int $days = 30, int $limit = 10): array
    {
        $from = Carbon::now()->subDays($days)->startOfDay();

        $referrers = AnalyticsHit::where('created_at', '>=', $from)
            ->whereNotNull('referrer')
            ->where('referrer', '!=', '')
            ->selectRaw('
                CASE
                    WHEN referrer LIKE "%google.%" THEN "Google"
                    WHEN referrer LIKE "%yandex.%" THEN "Yandex"
                    WHEN referrer LIKE "%vk.com%" THEN "VK"
                    WHEN referrer LIKE "%t.me%" THEN "Telegram"
                    WHEN referrer LIKE "%ok.ru%" THEN "Одноклассники"
                    WHEN referrer LIKE "%dzen.ru%" THEN "Дзен"
                    WHEN referrer LIKE "%facebook.%" THEN "Facebook"
                    WHEN referrer LIKE "%instagram.%" THEN "Instagram"
                    WHEN referrer LIKE "%twitter.%" THEN "Twitter"
                    ELSE SUBSTRING_INDEX(SUBSTRING_INDEX(referrer, "/", 3), "/", -1)
                END as source,
                COUNT(*) as hits,
                COUNT(DISTINCT session_id) as visitors
            ')
            ->groupBy('source')
            ->orderByDesc('hits')
            ->limit($limit)
            ->get()
            ->toArray();

        $directCount = AnalyticsHit::where('created_at', '>=', $from)
            ->where(function ($q) {
                $q->whereNull('referrer')->orWhere('referrer', '');
            })
            ->count();

        $results = array_map(fn($r) => [
            'source' => $r['source'],
            'hits' => (int) $r['hits'],
            'visitors' => (int) $r['visitors'],
        ], $referrers);

        if ($directCount > 0) {
            array_unshift($results, [
                'source' => 'Прямой заход',
                'hits' => $directCount,
                'visitors' => $directCount,
            ]);
        }

        return $results;
    }
}
