<?php

namespace App\Containers\Dashboard\UI\WEB\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJournalIssueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'path_file' => ['required', 'string'],
            'year_publication' => [
                'required',
                'integer',
                'min:1900',
                'max:' . (now()->year + 1),
            ],
            'is_active' => ['nullable', 'boolean'],
            'sort' => ['nullable', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Название выпуска обязательно для заполнения',
            'title.max' => 'Название выпуска не должно превышать 255 символов',
            'path_file.required' => 'Файл выпуска обязателен',
            'year_publication.required' => 'Год публикации обязателен',
            'year_publication.min' => 'Год должен быть не ранее 1900',
            'year_publication.max' => 'Год не может быть больше ' . (now()->year + 1),
        ];
    }
}
