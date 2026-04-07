<?php

namespace App\Containers\Dashboard\UI\WEB\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttachDepartmentProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'program_id' => ['required', 'exists:educational_programs,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'program_id.required' => 'Необходимо выбрать образовательную программу',
            'program_id.exists' => 'Выбранная программа не существует',
        ];
    }
}
