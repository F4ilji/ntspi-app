<?php

namespace App\Containers\Dashboard\Actions\Sliders;

use App\Containers\Widget\Models\Slider;
use App\Containers\Widget\Models\Slide;
use Illuminate\Support\Collection;

class ListSlidesAction
{
    public function run(Slider $slider): Collection
    {
        return $slider->slides()
            ->orderBy('sort')
            ->get();
    }
}
