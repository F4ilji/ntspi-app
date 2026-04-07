<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Article\Models\Post;
use App\Containers\Dashboard\Actions\Sliders\ListSlidesAction;
use App\Containers\Widget\Models\Slider;
use App\Http\Controllers\Controller;
use Inertia\Inertia;

class EditSliderController extends Controller
{
    public function __construct(
        private readonly ListSlidesAction $listSlidesAction,
    ) {}

    public function __invoke(Slider $slider): \Inertia\Response
    {
        $slider->load('slides');
        $slides = $this->listSlidesAction->run($slider);
        
        $posts = Post::where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->get(['id', 'title', 'slug']);

        return Inertia::render('Dashboard/Sliders/Edit', [
            'slider' => $slider,
            'slides' => $slides,
            'posts' => $posts,
        ]);
    }
}
