<?php

namespace App\Containers\Article\UI\WEB\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateSlideRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string|max:1000',
            'image' => 'required|array',
            'image.url' => 'required|string',
            'link' => 'required|string|max:255',
            'settings' => 'nullable|array',
            'settings.text_position' => 'nullable|string|in:left,center,right',
            'settings.link_text' => 'nullable|string|max:50',
            'settings.shading' => 'nullable|string',
            'color_theme' => 'required|string',
            'is_active' => 'boolean',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
        ];
    }
}
