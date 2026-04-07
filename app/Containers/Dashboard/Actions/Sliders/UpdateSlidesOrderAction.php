<?php

namespace App\Containers\Dashboard\Actions\Sliders;

use App\Containers\Widget\Models\Slider;

class UpdateSlidesOrderAction
{
    public function run(Slider $slider, array $slideIds): void
    {
        foreach ($slideIds as $index => $slideId) {
            $slider->slides()->where('id', $slideId)->update(['sort' => $index + 1]);
        }
    }
}
