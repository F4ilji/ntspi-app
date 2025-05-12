<?php

namespace App\Containers\Article\Tasks;

use App\Containers\Article\Models\Post;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class GetPostsTask
{
    public function run(array $filters): LengthAwarePaginator
    {
        $cacheKey = 'posts_' . md5(serialize($filters));

        return Cache::remember($cacheKey, now()->addHours(1), function () use ($filters) {
            $query = Post::query()
                ->with('category')
                ->select('title', 'slug', 'authors', 'category_id', 'preview', 'search_data', 'publish_at')
                ->where('status', 'published')
                ->where('publish_at', '<', Carbon::now())
                ->when(!empty($filters['tag']), function ($query) use ($filters) {
                    if (is_array($filters['tag'])) {
                        return $query->withAnyTags($filters['tag']);
                    }

                    $slugsArray = explode(',', $filters['tag']);
                    return $query->withAnyTags($slugsArray);
                })
                ->when(!empty($filters['category']), function ($query) use ($filters) {
                    if (is_array($filters['category'])) {
                        $query->whereHas('category', function ($query) use ($filters) {
                            $query->whereIn('slug', $filters['category']);
                        });
                    }
                })
                ->when(!empty($filters['search']), function ($query) use ($filters) {
                    $query->whereRaw('LOWER(title) like ?', ["%".strtolower($filters['search'])."%"]);
                });

            $sort = $filters['sort'] ?? 'desc';
            return $query->orderBy('publish_at', $sort)->paginate(9)->withQueryString();
        });
    }
}