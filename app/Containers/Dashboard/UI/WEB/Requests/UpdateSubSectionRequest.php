<?php

namespace App\Containers\Dashboard\UI\WEB\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubSectionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'unique:sub_sections,slug,' . $this->subSection->id, 'max:255'],
            'main_section_id' => ['nullable', 'exists:main_sections,id'],
            'page_ids' => ['nullable', 'array'],
            'page_ids.*' => ['exists:pages,id'],
        ];
    }
}
