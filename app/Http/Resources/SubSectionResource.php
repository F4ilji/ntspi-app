<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubSectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'pages' => ClientPageNavigateResource::collection($this->whenLoaded('pages')),
            'main_section' => $this->mainSection->title ?? null,
            'created_at' => $this->created_at->diffforhumans(),
        ];
    }
}
