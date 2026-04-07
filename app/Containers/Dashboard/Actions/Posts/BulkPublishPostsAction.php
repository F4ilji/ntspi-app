<?php

namespace App\Containers\Dashboard\Actions\Posts;

use App\Containers\Article\Enums\PostStatus;
use App\Containers\Dashboard\Tasks\Posts\BulkUpdatePostStatusTask;
use Carbon\Carbon;

class BulkPublishPostsAction
{
    public function __construct(
        private readonly BulkUpdatePostStatusTask $bulkUpdatePostStatusTask,
    ) {}

    public function run(array $ids): int
    {
        return $this->bulkUpdatePostStatusTask->run($ids, PostStatus::PUBLISHED, Carbon::now());
    }
}
