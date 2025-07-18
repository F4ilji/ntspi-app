<?php

namespace App\Containers\Search\Tasks;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class SortAndHighlightSearchResultsTask
{
    public function run(Collection $data, string $searchRequest): array
    {
        $sortedData = [];

        foreach ($data as $item) {
            $searchData = $item['search_data'] ?? '';
            $matches = $this->getMatches($searchData, $searchRequest);
            $sortedData[$item['type']][] = [
                'data' => $item,
                'matches' => $matches,
                'tag' => $item['type']
            ];
        }

        return $sortedData;
    }

    private function getMatches(string $haystack, string $needle): array
    {
        $matches = [];
        $offset = 0;
        if (($offset = mb_strpos($haystack, $needle, $offset, 'UTF-8')) !== false) {
            for ($i = 0; 1 > count($matches); $i++) {
                $left = max(0, $offset - 50);
                $right = min(mb_strlen($haystack, 'UTF-8'), $offset + 100);
                $excerpt = mb_substr($haystack, $left, $right - $left, 'UTF-8');
                $matches[] = $excerpt;
            }
        }

        return $matches;
    }
}
