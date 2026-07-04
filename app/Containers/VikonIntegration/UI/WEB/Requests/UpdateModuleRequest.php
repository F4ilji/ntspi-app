<?php

namespace App\Containers\VikonIntegration\UI\WEB\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateModuleRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }

    public function rules(): array
    {
        return [
            'module_id' => ['required', 'integer', Rule::in([1, 2, 6])],
        ];
    }
}
