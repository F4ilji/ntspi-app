<?php

namespace App\Containers\Dashboard\Actions\Sliders;

use App\Containers\Widget\Models\Slider;
use Illuminate\Support\Str;

class CreateSliderAction
{
    public function run(array $data): Slider
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        
        return Slider::create($data);
    }
}
