<?php

namespace App\Containers\Dashboard\Tasks\Posts;

use App\Containers\Article\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListPostsTask
{
    public function run(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = Post::with(['category', 'author'])
            ->orderBy('publish_at', 'desc');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }

        return $query->paginate($perPage);
    }
}
