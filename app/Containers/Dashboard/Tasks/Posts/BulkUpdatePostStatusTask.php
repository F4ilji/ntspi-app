<?php

namespace App\Containers\Dashboard\Tasks\Posts;

use App\Containers\Article\Enums\PostStatus;
use App\Containers\Article\Models\Post;
use Carbon\Carbon;

class BulkUpdatePostStatusTask
{
    public function run(array $ids, PostStatus $status, ?Carbon $publishAt = null): int
    {
        $data = ['status' => $status];

        if ($publishAt !== null) {
            $data['publish_at'] = $publishAt;
        }

        return Post::whereIn('id', $ids)->update($data);
    }
}
