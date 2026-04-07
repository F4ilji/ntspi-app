<?php

namespace App\Containers\Dashboard\Actions\ContactWidgets;

use App\Containers\Widget\Models\ContactWidget;
use Illuminate\Support\Str;

class CreateContactWidgetAction
{
    public function run(array $data): ContactWidget
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        $data['is_active'] = $data['is_active'] ?? true;

        return ContactWidget::create($data);
    }
}
