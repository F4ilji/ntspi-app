<?php

namespace App\Containers\Analytics\Tasks;

use App\Containers\Analytics\Models\AnalyticsHit;
use App\Containers\AppStructure\Models\MainSection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GetSectionsStatsTask
{
    public function run(int $days = 30): array
    {
        $from = Carbon::now()->subDays($days)->startOfDay();
        $sections = config('analytics.sections', []);

        $hits = AnalyticsHit::where('created_at', '>=', $from)
            ->selectRaw('url, session_id')
            ->get();

        $grouped = [];
        foreach ($hits as $hit) {
            $prefix = $this->resolvePrefix($hit->url, $sections);
            if (!isset($grouped[$prefix])) {
                $grouped[$prefix] = ['views' => 0, 'visitors' => []];
            }
            $grouped[$prefix]['views']++;
            $grouped[$prefix]['visitors'][$hit->session_id] = true;
        }

        $result = [];
        foreach ($grouped as $prefix => $data) {
            $config = $sections[$prefix] ?? null;
            $result[] = [
                'prefix' => $prefix,
                'label' => $config['label'] ?? $prefix,
                'icon' => $config['icon'] ?? 'folder',
                'views' => $data['views'],
                'visitors' => count($data['visitors']),
                'order' => $config['order'] ?? 99,
            ];
        }

        usort($result, fn($a, $b) => $a['order'] <=> $b['order']);

        return $result;
    }

    private function resolvePrefix(string $url, array $sections): string
    {
        $path = parse_url($url, PHP_URL_PATH) ?: '/';
        $segments = explode('/', trim($path, '/'));
        $firstSegment = '/' . ($segments[0] ?? '');

        if (isset($sections[$firstSegment])) {
            return $firstSegment;
        }

        if ($firstSegment === '/' && $path === '/') {
            return '/';
        }

        // Check main sections for CMS pages
        $mainSection = MainSection::where('slug', $segments[0] ?? '')->first();
        if ($mainSection) {
            return '/page';
        }

        return '/other';
    }
}
