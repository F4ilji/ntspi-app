<?php

namespace App\Containers\Main\UI\WEB\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MainPageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'educations' => $this->educations,
            'posts' => $this->posts,
            'events' => $this->events,
            'seo' => $this->seo,
        ];
    }
}