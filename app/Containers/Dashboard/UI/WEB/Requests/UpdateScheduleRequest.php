<?php

namespace App\Containers\Dashboard\UI\WEB\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'educational_group_id' => ['required', 'exists:educational_groups,id'],
            'file' => ['sometimes', 'array', 'min:1', 'max:1'],
            'file.0.title' => ['required_with:file', 'string', 'max:255'],
            'file.0.path' => ['required_with:file', 'file', 'mimes:pdf', 'max:10000'],
        ];
    }

    public function messages(): array
    {
        return [
            'educational_group_id.required' => 'Необходимо выбрать учебную группу',
            'educational_group_id.exists' => 'Выбранная учебная группа не существует',
            'file.0.title.required' => 'Необходимо указать название файла',
            'file.0.path.required' => 'Необходимо загрузить PDF файл',
            'file.0.path.mimes' => 'Файл должен быть в формате PDF',
            'file.0.path.max' => 'Размер файла не должен превышать 10MB',
        ];
    }
}
