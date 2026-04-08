<?php

namespace App\Containers\VikonIntegration\UI\WEB\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DownloadModuleUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'module_id' => ['required', 'integer', Rule::in([1, 2, 6])],
        ];
    }

    public function messages(): array
    {
        return [
            'module_id.required' => 'Идентификатор модуля обязателен.',
            'module_id.integer' => 'Идентификатор модуля должен быть числом.',
            'module_id.in' => 'Неподдерживаемый идентификатор модуля. Допустимы: 1 (Сведения), 2 (Абитуриент), 6 (ВСОКО).',
        ];
    }
}
