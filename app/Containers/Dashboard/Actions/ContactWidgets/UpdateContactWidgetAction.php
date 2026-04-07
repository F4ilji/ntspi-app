<?php

namespace App\Containers\Dashboard\Actions\ContactWidgets;

use App\Containers\Widget\Models\ContactWidget;

class UpdateContactWidgetAction
{
    public function run(ContactWidget $widget, array $data): ContactWidget
    {
        $widget->update($data);

        return $widget;
    }
}
