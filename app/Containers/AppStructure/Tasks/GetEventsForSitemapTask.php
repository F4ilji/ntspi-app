<?php

namespace App\Containers\AppStructure\Tasks;

use App\Containers\Event\Models\Event;

class GetEventsForSitemapTask
{
    public function run()
    {
        $now = now()->toDateString();

        return Event::query()
            ->where('event_date_end', '>=', $now)
            ->get();
    }
}
