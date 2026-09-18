<?php

namespace App\Containers\Analytics\Tasks;

use App\Containers\Analytics\Models\AnalyticsHit;
use App\Containers\Analytics\Models\AnalyticsSession;
use App\Containers\AppStructure\Models\MainSection;
use App\Containers\AppStructure\Models\Page;
use App\Containers\AppStructure\Models\SubSection;
use Illuminate\Support\Carbon;

class GetSectionDetailTask
{
    public function run(string $prefix, int $days = 30): array
    {
        $from = Carbon::now()->subDays($days)->startOfDay();

        $hits = AnalyticsHit::where('created_at', '>=', $from)
            ->where('url', 'like', $prefix . '%')
            ->selectRaw('url, session_id, created_at, referrer')
            ->get();

        $totalViews = $hits->count();
        $uniqueVisitors = $hits->pluck('session_id')->unique()->count();

        $mainSection = null;
        $subSection = null;
        $page = null;
        $children = [];
        $breadcrumbs = [];
        $type = 'unknown';
        $exitPages = [];

        $cleanPrefix = ltrim($prefix, '/');
        $parts = array_filter(explode('/', $cleanPrefix));

        // 1. Check if MainSection
        $mainSection = MainSection::where('slug', $cleanPrefix)->first();
        if ($mainSection) {
            $type = 'main_section';
            $breadcrumbs = [['title' => 'Аналитика', 'url' => route('dashboard.analytics.index')]];
            $subSections = SubSection::where('main_section_id', $mainSection->id)
                ->with('pages')
                ->orderBy('sort')
                ->get();

            foreach ($subSections as $ss) {
                $ssPrefix = '/' . $mainSection->slug . '/' . $ss->slug;
                $ssHits = $hits->filter(fn($h) => str_starts_with(parse_url($h->url, PHP_URL_PATH) ?: '', $ssPrefix));
                $children[] = [
                    'id' => $ss->id,
                    'title' => $ss->title,
                    'prefix' => $ssPrefix,
                    'views' => $ssHits->count(),
                    'visitors' => $ssHits->pluck('session_id')->unique()->count(),
                    'pages_count' => $ss->pages->count(),
                ];
            }
        }
        // 2. Check if SubSection (2 parts)
        elseif (count($parts) === 2) {
            $mainSection = MainSection::where('slug', $parts[0])->first();
            $subSection = SubSection::where('slug', $parts[1])
                ->where('main_section_id', $mainSection?->id)
                ->first();

            if ($subSection && $mainSection) {
                $type = 'sub_section';
                $breadcrumbs = [
                    ['title' => 'Аналитика', 'url' => route('dashboard.analytics.index')],
                    ['title' => $mainSection->title, 'url' => route('dashboard.analytics.section', ['prefix' => '/' . $mainSection->slug])],
                ];

                $pages = Page::where('sub_section_id', $subSection->id)
                    ->orderBy('sort')
                    ->get();

                foreach ($pages as $p) {
                    $pUrl = '/' . $p->path;
                    $pHits = $hits->filter(fn($h) => rtrim(parse_url($h->url, PHP_URL_PATH) ?: '', '/') === $pUrl);
                    $children[] = [
                        'id' => $p->id,
                        'title' => $p->title ?: $p->slug,
                        'url' => $pUrl,
                        'views' => $pHits->count(),
                        'visitors' => $pHits->pluck('session_id')->unique()->count(),
                    ];
                }
            }
        }

        // 3. Check if Page (by path or 3+ parts)
        if ($type === 'unknown') {
            $page = Page::where('path', $cleanPrefix)->first();
            if ($page && $page->section) {
                $subSection = $page->section;
                $mainSection = $subSection->mainSection;
            }

            if ($mainSection && $subSection) {
                $type = 'page';
                $breadcrumbs = [
                    ['title' => 'Аналитика', 'url' => route('dashboard.analytics.index')],
                    ['title' => $mainSection->title, 'url' => route('dashboard.analytics.section', ['prefix' => '/' . $mainSection->slug])],
                    ['title' => $subSection->title, 'url' => route('dashboard.analytics.section', ['prefix' => '/' . $mainSection->slug . '/' . $subSection->slug])],
                ];

                // Exit pages: URLs visited after this page in the same session
                $exitPages = $this->getExitPages($hits, $prefix, $days);
            }
        }

        $label = $cleanPrefix;
        if ($mainSection) $label = $mainSection->title;
        if ($subSection) $label = $subSection->title;
        if ($page) $label = $page->title ?: $page->slug;

        $topPages = $hits->groupBy('url')
            ->map(fn($group) => [
                'url' => $group->first()->url,
                'views' => $group->count(),
                'unique_visitors' => $group->pluck('session_id')->unique()->count(),
            ])
            ->sortByDesc('views')
            ->values()
            ->take(15)
            ->toArray();

        $dailyStats = $hits->groupBy(fn($h) => $h->created_at->format('Y-m-d'))
            ->map(fn($group, $date) => [
                'date' => $date,
                'views' => $group->count(),
                'visitors' => $group->pluck('session_id')->unique()->count(),
            ])
            ->values()
            ->sortBy('date')
            ->toArray();

        $referrers = $hits->filter(fn($h) => !empty($h->referrer))
            ->groupBy(function ($h) {
                $url = $h->referrer;
                if (str_contains($url, 'google.')) return 'Google';
                if (str_contains($url, 'yandex.')) return 'Yandex';
                if (str_contains($url, 'vk.com')) return 'VK';
                if (str_contains($url, 't.me')) return 'Telegram';
                if (str_contains($url, 'ok.ru')) return 'Одноклассники';
                if (str_contains($url, 'dzen.ru')) return 'Дзен';
                return parse_url($url, PHP_URL_HOST) ?? 'Другое';
            })
            ->map(fn($group) => [
                'source' => $group->first()->referrer,
                'hits' => $group->count(),
                'visitors' => $group->pluck('session_id')->unique()->count(),
            ])
            ->sortByDesc('hits')
            ->values()
            ->take(10)
            ->toArray();

        $devices = AnalyticsSession::where('started_at', '>=', $from)
            ->whereIn('id', $hits->pluck('session_id')->unique())
            ->selectRaw('device_type, COUNT(*) as count')
            ->groupBy('device_type')
            ->pluck('count', 'device_type')
            ->toArray();

        return [
            'prefix' => $prefix,
            'type' => $type,
            'label' => $label,
            'breadcrumbs' => $breadcrumbs,
            'children' => $children,
            'exit_pages' => $exitPages,
            'total_views' => $totalViews,
            'unique_visitors' => $uniqueVisitors,
            'top_pages' => $topPages,
            'daily_stats' => $dailyStats,
            'referrers' => $referrers,
            'devices' => $devices,
        ];
    }

    private function getExitPages($hits, string $prefix, int $days): array
    {
        $sessionIds = $hits->pluck('session_id')->unique();

        $allHits = AnalyticsHit::where('created_at', '>=', now()->subDays($days)->startOfDay())
            ->whereIn('session_id', $sessionIds)
            ->orderBy('session_id')
            ->orderBy('created_at')
            ->selectRaw('session_id, url, created_at')
            ->get()
            ->groupBy('session_id');

        $exitCounts = [];
        foreach ($allHits as $sessionId => $sessionHits) {
            $sorted = $sessionHits->values();
            for ($i = 0; $i < $sorted->count() - 1; $i++) {
                $currentUrl = parse_url($sorted[$i]->url, PHP_URL_PATH) ?: '/';
                if (str_starts_with($currentUrl, $prefix)) {
                    $nextUrl = parse_url($sorted[$i + 1]->url, PHP_URL_PATH) ?: '/';
                    if (!isset($exitCounts[$nextUrl])) {
                        $exitCounts[$nextUrl] = 0;
                    }
                    $exitCounts[$nextUrl]++;
                }
            }
        }

        arsort($exitCounts);
        return array_slice(array_map(fn($url, $count) => [
            'url' => $url,
            'count' => $count,
        ], array_keys($exitCounts), $exitCounts), 0, 10);
    }
}
