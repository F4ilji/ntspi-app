<?php

namespace App\Containers\Dashboard\Actions\Sliders;

use App\Containers\Widget\Models\Slider;

class UpdateSliderAction
{
    public function run(Slider $slider, array $data): Slider
    {
        $slider->update($data);
        
        return $slider;
    }
}
