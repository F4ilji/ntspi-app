<?php

namespace App\Containers\Dashboard\UI\WEB\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserDetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_only_worker' => ['nullable', 'boolean'],
            'photo' => ['nullable', 'file', 'image', 'max:10240'], // 10MB max
            'contactEmail' => ['nullable', 'email', 'max:255'],
            'contactPhone' => ['nullable', 'string', 'max:255'],
            'academicTitle' => ['nullable', 'string', 'max:255'],
            'AcademicDegree' => ['nullable', 'string', 'max:255'],
            'workExperience' => ['nullable'],
            'education' => ['nullable'],
            'professionalRetraining' => ['nullable'],
            'professionalDevelopment' => ['nullable'],
            'awards' => ['nullable'],
            'professDisciplines' => ['nullable'],
            'attendedConferences' => ['nullable'],
            'publications' => ['nullable'],
            'participationScienceProjects' => ['nullable'],
            'other' => ['nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'contactEmail.email' => 'Некорректный формат email',
        ];
    }
}
