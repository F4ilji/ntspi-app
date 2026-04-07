<?php

namespace App\Containers\Dashboard\Actions\Posts;

use App\Containers\Dashboard\Tasks\Posts\GetPostFormDataTask;

class GetPostFormDataAction
{
    public function __construct(
        private readonly GetPostFormDataTask $getPostFormDataTask,
    ) {}

    public function run(): array
    {
        return $this->getPostFormDataTask->run();
    }
}
