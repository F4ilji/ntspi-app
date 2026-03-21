<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Dashboard\Tasks\GetAiPreparedPostsTask;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IndexDashboardController extends Controller
{
    public function __construct(
        private readonly GetAiPreparedPostsTask $getAiPreparedPostsTask,
    ) {}

    public function __invoke(Request $request): \Inertia\Response
    {
        $aiPreparedPosts = $this->getAiPreparedPostsTask->run();

        return inertia()->render('Dashboard/Main', [
            'aiPreparedPosts' => $aiPreparedPosts,
        ]);
    }
}