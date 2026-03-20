<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Dashboard\Tasks\GetDraftPostsTask;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IndexDashboardController extends Controller
{
    public function __construct(
        private readonly GetDraftPostsTask $getDraftPostsTask,
    ) {}

    public function __invoke(Request $request): \Inertia\Response
    {
        $draftPosts = $this->getDraftPostsTask->run();

        return inertia()->render('Dashboard/Main', [
            'draftPosts' => $draftPosts,
        ]);
    }
}