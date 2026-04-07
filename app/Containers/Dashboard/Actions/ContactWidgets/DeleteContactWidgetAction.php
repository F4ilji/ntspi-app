<?php

namespace App\Containers\Dashboard\Actions\ContactWidgets;

use App\Containers\Widget\Models\ContactWidget;

class DeleteContactWidgetAction
{
    public function run(ContactWidget $widget): bool
    {
        return $widget->delete();
    }
}
