<?php

namespace App\Containers\Dashboard\UI\WEB\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFacultyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('faculties')->ignore($this->faculty)],
            'abbreviation' => ['required', 'string', 'max:10'],
            'is_active' => ['boolean'],
            'content' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Название факультета обязательно для заполнения',
            'title.max' => 'Название не должно превышать 255 символов',
            'slug.required' => 'URL-адрес обязателен для заполнения',
            'slug.unique' => 'Такой URL уже используется',
            'abbreviation.required' => 'Аббревиатура обязательна',
            'abbreviation.max' => 'Аббревиатура не должна превышать 10 символов',
        ];
    }
}
