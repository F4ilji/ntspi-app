<?php

namespace App\Http\Controllers;

use App\Enums\CacheKeys;
use App\Http\Resources\ClientContactWidgetResource;
use App\Http\Resources\ClientPageReferenceListResource;
use App\Models\ContactWidget;
use Illuminate\Support\Facades\Cache;

class ClientWidgetContactController extends Controller
{
    public function show(string $slug)
    {
        return Cache::remember(
            CacheKeys::CONTACT_WIDGET_PREFIX->value . $slug,
            now()->addHours(12), // Кешируем на 12 часов
            function () use ($slug) {
                return new ClientContactWidgetResource(
                    ContactWidget::query()
                        ->where('slug', $slug)
                        ->firstOrFail()
                );
            }
        );
    }
}
