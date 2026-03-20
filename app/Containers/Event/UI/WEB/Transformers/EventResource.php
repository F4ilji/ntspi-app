<?php

namespace App\Containers\Event\UI\WEB\Transformers;

use App\Ship\Resources\JsonResource;
use Illuminate\Support\Carbon;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'event_date_start' => $this->event_date_start,
            'event_time_start' => Carbon::parse($this->event_time_start)->format('H:i'),
            'address' => $this->address,
            'is_online' => $this->is_online,
            'category' => $this->category ?? null,
        ];
    }
}
