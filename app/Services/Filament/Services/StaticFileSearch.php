<?php

namespace App\Services\Filament\Services;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class StaticFileSearch
{
    const CACHE_KEY = 'static_html_index_v2';
    const FILES_DIR = 'sveden';
    const CACHE_TTL = 86400; // 24 часа
    const PER_PAGE = 10; // Количество результатов на страницу

    public function search(string $query): array
    {
        if ($query === null) {
            return [
              'data' => null
            ];
        }
        $page = request()->input('page', 1);
        try {
            $index = $this->getIndex();
            $results = [];
            $normalizedQuery = $this->normalizeText(Str::lower($query));

            foreach ($index as $filePath => $content) {
                if (stripos($content['content'], $normalizedQuery) !== false) {
                    $relativePath = str_replace(public_path() . '/', '', $filePath);
                    $results[] = [
                        'file' => $relativePath,
                        'content' => $content['title'],
                        'category' => trim($content['category']),
                    ];
                }
            }
            return $this->paginateResults($results, $page);
        } catch (\Exception $e) {
            Log::error('Search error: ' . $e->getMessage());
            return [
                'data' => [],
                'meta' => [
                    'current_page' => 1,
                    'total' => 0,
                    'per_page' => self::PER_PAGE,
                    'last_page' => 1
                ]
            ];
        }
    }

    protected function paginateResults(array $results, int $page): array
    {
        $total = count($results);
        $lastPage = max(1, ceil($total / self::PER_PAGE));
        $page = max(1, min($page, $lastPage));

        $offset = ($page - 1) * self::PER_PAGE;
        $paginatedResults = array_slice($results, $offset, self::PER_PAGE);

        return [
            'data' => $paginatedResults,
            'meta' => [
                'current_page' => $page,
                'total' => $total,
                'per_page' => self::PER_PAGE,
                'last_page' => $lastPage
            ]
        ];
    }

    protected function getIndex(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function() {
            try {
                $index = [];
                $directory = public_path(self::FILES_DIR);

                if (!is_dir($directory)) {
                    Log::error("Directory not found: {$directory}");
                    return [];
                }

                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::SELF_FIRST
                );

                foreach ($iterator as $file) {
                    if ($file->isFile() && $this->isHtmlFile($file)) {
                        $content = file_get_contents($file->getPathname());
                        if ($content !== false) {
                            $breadcrumb = app(BreadcrumbFinderService::class)->isSameCategory($content);
                            $text = $this->normalizeText(strip_tags($content));
                            $index[$file->getPathname()] = [
                                'title' => $this->getFirstH1Content($content),
                                'content' => $text,
                                'category' => $breadcrumb ?? null,
                            ];
                        }
                    }
                }

                return $index;
            } catch (\Exception $e) {
                Log::error('Index creation error: ' . $e->getMessage());
                return [];
            }
        });
    }

    protected function isHtmlFile(\SplFileInfo $file): bool
    {
        $extension = strtolower($file->getExtension());
        return in_array($extension, ['html', 'htm']);
    }

    protected function normalizeText(string $text): string
    {
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text);
        $text = trim($text);
        return Str::lower($text);
    }

    protected function findMatches(string $content, string $query): array
    {
        $matches = [];
        $offset = 0;
        $query = Str::lower($query);
        $content = Str::lower($content);
        $queryLength = mb_strlen($query, 'UTF-8');

        while (($offset = mb_strpos($content, $query, $offset, 'UTF-8')) !== false) {
            $start = max(0, $offset - 50);
            $length = min(150, mb_strlen($content) - $start);
            $excerpt = mb_substr($content, $start, $length, 'UTF-8');

            $matches[] = str_replace(
                $query,
                '[[HIGHLIGHT]]'.$query.'[[/HIGHLIGHT]]',
                $excerpt
            );

            $offset += $queryLength;
        }

        return $matches;
    }

    public function clearCache(): bool
    {
        try {
            Cache::forget(self::CACHE_KEY);
            return true;
        } catch (\Exception $e) {
            Log::error('Cache clear error: ' . $e->getMessage());
            return false;
        }
    }

    public function rebuildIndex(): array
    {
        $this->clearCache();
        return $this->getIndex();
    }

    public function getCacheStatus(): array
    {
        return [
            'exists' => Cache::has(self::CACHE_KEY),
            'ttl' => Cache::get(self::CACHE_KEY.'_ttl', null),
            'driver' => config('cache.default'),
            'path' => public_path(self::FILES_DIR),
            'directory_exists' => is_dir(public_path(self::FILES_DIR))
        ];
    }

    public function getFirstH1Content(string $content): ?string
    {
        try {
            if (preg_match('/<h1[^>]*>(.*?)<\/h1>/is', $content, $matches)) {
                return $this->normalizeText($matches[1]);
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Failed to get H1: ' . $e->getMessage());
            return null;
        }
    }
}