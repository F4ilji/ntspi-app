<?php

namespace App\Containers\Article\UI\WEB\Transformers;


use App\Ship\Requests\Request;
use App\Ship\Resources\JsonResource;

class TagResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
        ];
    }
}
