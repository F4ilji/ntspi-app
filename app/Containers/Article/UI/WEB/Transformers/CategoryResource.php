<?php

namespace App\Containers\Article\UI\WEB\Transformers;



use App\Ship\Resources\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \App\Ship\Requests\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'created_at' => $this->created_at->diffforhumans(),
        ];
    }
}
