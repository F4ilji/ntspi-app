<?php

namespace App\Containers\Widget\Actions;

use App\Containers\Widget\Tasks\FindContactWidgetBySlugTask;
use App\Containers\Widget\UI\API\Transformers\ContactWidgetResource;
use App\Ship\Enums\CacheKeys;
use Illuminate\Support\Facades\Cache;

class GetContactWidgetPageAction
{
    public function __construct(private FindContactWidgetBySlugTask $findContactWidgetBySlugTask)
    {
    }

    public function run(string $slug): ContactWidgetResource
    {
        return Cache::remember(
            CacheKeys::CONTACT_WIDGET_PREFIX->value . $slug,
            now()->addHours(12),
            function () use ($slug) {
                return new ContactWidgetResource(
                    $this->findContactWidgetBySlugTask->run($slug)
                );
            }
        );
    }
}
