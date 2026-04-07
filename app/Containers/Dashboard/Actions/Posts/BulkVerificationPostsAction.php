<?php

namespace App\Containers\Dashboard\Actions\Posts;

use App\Containers\Article\Enums\PostStatus;
use App\Containers\Dashboard\Tasks\Posts\BulkUpdatePostStatusTask;

class BulkVerificationPostsAction
{
    public function __construct(
        private readonly BulkUpdatePostStatusTask $bulkUpdatePostStatusTask,
    ) {}

    public function run(array $ids): int
    {
        return $this->bulkUpdatePostStatusTask->run($ids, PostStatus::VERIFICATION);
    }
}
