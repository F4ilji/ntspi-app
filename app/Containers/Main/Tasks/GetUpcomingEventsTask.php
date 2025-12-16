<?php

namespace App\Containers\Main\Tasks;

use App\Containers\Event\Models\Event;
use App\Containers\Event\UI\WEB\Transformers\EventThumbnailResource;
use DateTime;
use Illuminate\Support\Facades\Cache;

class GetUpcomingEventsTask
{
    public function run()
    {
        return Cache::remember('upcoming_events', now()->addHour(), function () {
            return $this->getUpcomingEvents();
        });
    }

    private function getUpcomingEvents()
    {
        $event_date_start = (new DateTime())->format('Y-m-d');

        return EventThumbnailResource::collection(
            Event::select('title', 'slug', 'event_date_start', 'event_time_start' , 'address', 'is_online', 'category_id')
                ->where('event_date_start', '>=', $event_date_start)
                ->orderBy('event_date_start', 'asc')
                ->limit(3)
                ->get()
        );
    }
}