<?php

namespace App\Containers\Widget\UI\API\Controllers;

use App\Containers\Widget\Actions\GetPostsAction;
use App\Containers\Widget\Actions\GetSinglePostAction;
use App\Ship\Controllers\Controller;
use Illuminate\Http\Request;

class ClientWidgetPostController extends Controller
{
    public function __construct(
        private readonly GetPostsAction $getPostsAction,
        private readonly GetSinglePostAction $getSinglePostAction
    ) {}

    public function index(Request $request)
    {
        $category_id = $request->input('category');
        $count = $request->input('count', 5);

        return $this->getPostsAction->run($category_id, $count);
    }

    public function single(int $id)
    {
        return $this->getSinglePostAction->run($id);
    }
}
