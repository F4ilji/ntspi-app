<?php

namespace App\Containers\Widget\UI\API\Controllers;

use App\Containers\Widget\Models\ContactWidget;
use App\Containers\Widget\UI\API\Transformers\ContactWidgetResource;
use App\Ship\Controllers\Controller;
use App\Ship\Enums\CacheKeys;
use Illuminate\Support\Facades\Cache;

class ClientWidgetContactController extends Controller
{
    public function show(string $slug)
    {
        return Cache::remember(
            CacheKeys::CONTACT_WIDGET_PREFIX->value . $slug,
            now()->addHours(12), // Кешируем на 12 часов
            function () use ($slug) {
                return new ContactWidgetResource(
                    ContactWidget::query()
                        ->where('slug', $slug)
                        ->firstOrFail()
                );
            }
        );
    }
}
