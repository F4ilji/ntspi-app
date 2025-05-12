<?php

namespace App\Ship\Resources\Breadcrumb;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientBreadcrumbPage extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'path' => $this->path
        ];
    }
}
