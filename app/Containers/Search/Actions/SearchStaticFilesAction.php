<?php

namespace App\Containers\Search\Actions;

use App\Containers\Search\Tasks\BuildStaticFileIndexTask;
use App\Containers\Search\Tasks\PaginateSearchResultsTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SearchStaticFilesAction
{
    const PER_PAGE = 10; // Количество результатов на страницу

    public function run(Request $request): array
    {
        $query = $request->input('search');
        $category = $request->input('category');
        $page = $request->input('page', 1);

        try {
            $index = app(BuildStaticFileIndexTask::class)->run();
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

            $categories = array_values(array_unique(array_filter(array_column($results, 'category'))));

            if ($category !== null) {
                $results = array_filter($results, function($item) use ($category) {
                    return $item['category'] === $category;
                });

                // Переиндексировать массив
                $results = array_values($results);
            }

            return app(PaginateSearchResultsTask::class)->run($results, $page, self::PER_PAGE, $categories);
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

    protected function normalizeText(string $text): string
    {
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text);
        $text = trim($text);
        return Str::lower($text);
    }

    protected function getFirstH1Content(string $content): ?string
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
