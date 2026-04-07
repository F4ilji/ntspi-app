<?php

namespace App\Containers\Dashboard\UI\WEB\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSlideRequest extends FormRequest
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
            'image' => 'sometimes',
            'link' => 'sometimes|required|string|max:255',
            'settings' => 'nullable|array',
            'settings.text_position' => 'nullable|string|in:left,center,right',
            'settings.link_text' => 'nullable|string|max:50',
            'settings.shading' => 'nullable|string',
            'settings.active_button' => 'nullable|in:0,1,true,false',
            'color_theme' => 'sometimes|required|string',
            'is_active' => 'nullable|in:0,1,true,false',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
        ];
    }
}
