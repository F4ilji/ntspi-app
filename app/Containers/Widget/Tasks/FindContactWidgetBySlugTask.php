<?php

namespace App\Containers\Widget\Tasks;

use App\Containers\Widget\Models\ContactWidget;
use App\Ship\Exceptions\NotFoundException;

class FindContactWidgetBySlugTask
{
    public function run(string $slug): ContactWidget
    {
        $contactWidget = ContactWidget::query()
            ->where('slug', $slug)
            ->firstOrFail();

        return $contactWidget;
    }
}
