<?php

namespace App\Containers\Analytics\Tasks;

use App\Containers\Analytics\Models\AnalyticsHit;
use App\Containers\AppStructure\Models\MainSection;
use Illuminate\Support\Carbon;

class GetNavigationSectionsTask
{
    public function run(int $days = 30): array
    {
        $from = Carbon::now()->subDays($days)->startOfDay();

        $hits = AnalyticsHit::where('created_at', '>=', $from)
            ->selectRaw('url, session_id')
            ->get();

        $mainSections = MainSection::with('subSections.pages')
            ->orderBy('sort')
            ->get();

        $sections = [];
        $cmsSlugs = [];

        // 1. CMS sections (MainSection → SubSection → Page)
        foreach ($mainSections as $ms) {
            $msPrefix = '/' . $ms->slug;
            $cmsSlugs[] = $msPrefix;
            $msHits = $hits->filter(fn($h) => str_starts_with(rtrim(parse_url($h->url, PHP_URL_PATH) ?: '', '/'), $msPrefix));
            $subSections = [];

            foreach ($ms->subSections as $ss) {
                $ssPrefix = $msPrefix . '/' . $ss->slug;
                $ssHits = $msHits->filter(fn($h) => str_starts_with(rtrim(parse_url($h->url, PHP_URL_PATH) ?: '', '/'), $ssPrefix));
                $pages = [];

                foreach ($ss->pages as $page) {
                    $pagePath = '/' . $page->path;
                    $pHits = $ssHits->filter(fn($h) => rtrim(parse_url($h->url, PHP_URL_PATH) ?: '', '/') === $pagePath);

                    $pages[] = [
                        'id' => $page->id,
                        'title' => $page->title ?: $page->slug,
                        'path' => $page->path,
                        'url' => $pagePath,
                        'views' => $pHits->count(),
                        'visitors' => $pHits->pluck('session_id')->unique()->count(),
                    ];
                }

                $subSections[] = [
                    'id' => $ss->id,
                    'title' => $ss->title,
                    'slug' => $ss->slug,
                    'views' => $ssHits->count(),
                    'visitors' => $ssHits->pluck('session_id')->unique()->count(),
                    'pages' => $pages,
                ];
            }

            $sections[] = [
                'id' => $ms->id,
                'title' => $ms->title,
                'slug' => $ms->slug,
                'group' => 'structure',
                'views' => $msHits->count(),
                'visitors' => $msHits->pluck('session_id')->unique()->count(),
                'sub_sections' => $subSections,
            ];
        }

        // 2. Config-defined sections (non-CMS)
        $configSections = config('analytics.sections', []);
        foreach ($configSections as $prefix => $config) {
            if ($prefix === '/') continue;
            if (in_array($prefix, $cmsSlugs)) continue;

            $sectionHits = $hits->filter(fn($h) => str_starts_with(rtrim(parse_url($h->url, PHP_URL_PATH) ?: '', '/'), $prefix));
            $subSections = [];

            if (isset($config['children'])) {
                $model = $config['children']['model'];
                $nameKey = $config['children']['name_key'];
                $slugKey = $config['children']['slug_key'];
                $children = $model::all();

                foreach ($children as $child) {
                    $childSlug = $child->{$slugKey};
                    $childPrefix = $prefix . '/' . $childSlug;
                    $childHits = $sectionHits->filter(fn($h) => str_starts_with(rtrim(parse_url($h->url, PHP_URL_PATH) ?: '', '/'), $childPrefix));

                    $subSections[] = [
                        'id' => $child->id,
                        'title' => $child->{$nameKey},
                        'slug' => $childSlug,
                        'views' => $childHits->count(),
                        'visitors' => $childHits->pluck('session_id')->unique()->count(),
                        'pages' => [],
                    ];
                }
            }

            $sections[] = [
                'id' => 'config:' . $prefix,
                'title' => $config['label'],
                'slug' => ltrim($prefix, '/'),
                'group' => $config['group'] ?? 'other',
                'views' => $sectionHits->count(),
                'visitors' => $sectionHits->pluck('session_id')->unique()->count(),
                'sub_sections' => $subSections,
            ];
        }

        // Homepage — as first item in structure group
        $homeHits = $hits->filter(fn($h) => (rtrim(parse_url($h->url, PHP_URL_PATH) ?: '', '/') === '/'));

        array_unshift($sections, [
            'id' => 'home',
            'title' => 'Главная страница',
            'slug' => '',
            'group' => 'structure',
            'type' => 'home',
            'views' => $homeHits->count(),
            'visitors' => $homeHits->pluck('session_id')->unique()->count(),
            'sub_sections' => [],
        ]);

        return [
            'sections' => $sections,
            'groupLabels' => config('analytics.group_labels', []),
        ];
    }
}
