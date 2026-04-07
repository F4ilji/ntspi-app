<?php

namespace App\Containers\Dashboard\Actions\Sliders;

use App\Containers\Widget\Models\Slide;

class DeleteSlideAction
{
    public function run(Slide $slide): bool
    {
        return $slide->delete();
    }
}
