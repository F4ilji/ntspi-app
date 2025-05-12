<?php

namespace App\Containers\Article\UI\WEB\Controllers;

use App\Containers\Article\Actions\ListPostsAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IndexPostController extends Controller
{
    public function __construct(
        private readonly ListPostsAction $listPostsAction,
    ) {}

    public function __invoke(Request $request)
    {
        $filters = $request->only(['search', 'category', 'tag', 'sort', 'page']);

        $data = $this->listPostsAction->run($filters);

        return inertia()->render('Client/Posts/Index', $data);
    }
}