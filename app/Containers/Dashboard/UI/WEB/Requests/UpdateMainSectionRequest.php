<?php

namespace App\Containers\Dashboard\UI\WEB\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMainSectionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'unique:main_sections,slug,' . $this->mainSection->id, 'max:255'],
        ];
    }
}
