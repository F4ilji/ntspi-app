<?php

namespace App\Containers\Dashboard\Actions\Posts;

use App\Containers\Dashboard\Tasks\Posts\GetAiPreparedPostsTask;
use Illuminate\Database\Eloquent\Collection;

class ListAiPreparedPostsAction
{
    public function __construct(
        private readonly GetAiPreparedPostsTask $getAiPreparedPostsTask,
    ) {}

    public function run(): Collection
    {
        return $this->getAiPreparedPostsTask->run();
    }
}
