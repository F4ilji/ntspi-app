<?php

namespace App\Containers\Main\Actions;

use App\Containers\Main\Tasks\GetEducationsDataTask;
use App\Containers\Main\Tasks\GetRecentPostsTask;
use App\Containers\Main\Tasks\GetUpcomingEventsTask;
use App\Containers\Main\Tasks\GetPageDataTask;

class GetMainPageDataAction
{
    public function __construct(
        private readonly GetEducationsDataTask $getEducationsDataTask,
        private readonly GetRecentPostsTask $getRecentPostsTask,
        private readonly GetUpcomingEventsTask $getUpcomingEventsTask,
        private readonly GetPageDataTask $getPageDataTask,
    ) {}

    public function run(): array
    {
        $educations = $this->getEducationsDataTask->run();
        $posts = $this->getRecentPostsTask->run();
        $events = $this->getUpcomingEventsTask->run();
        $page = $this->getPageDataTask->run();

        $seo = $page->seo ?? null;

        return [
            'educations' => $educations,
            'posts' => $posts,
            'events' => $events,
            'seo' => $seo
        ];
    }
}