<?php

namespace App\Containers\Dashboard\Actions\Sliders;

use App\Containers\Widget\Models\Slider;

class DeleteSliderAction
{
    public function run(Slider $slider): bool
    {
        return $slider->delete();
    }
}
