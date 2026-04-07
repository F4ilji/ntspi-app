<?php

namespace App\Containers\Dashboard\UI\WEB\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubSectionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'unique:sub_sections,slug', 'max:255'],
            'main_section_id' => ['nullable', 'exists:main_sections,id'],
        ];
    }
}
