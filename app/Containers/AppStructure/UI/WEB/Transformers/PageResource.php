<?php

namespace App\Containers\AppStructure\UI\WEB\Transformers;

use App\Ship\Resources\JsonResource;

class PageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \App\Ship\Requests\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'slug' => $this->slug,
            'code' => $this->code,
            'path' => $this->path,
            'is_url' => $this->is_url,
            'settings' => $this->settings,
            'icon' => $this->icon,
            'section' => $this->section ? $this->section->title : null,
            'created_at' => $this->created_at->diffforhumans()
        ];
    }
}
