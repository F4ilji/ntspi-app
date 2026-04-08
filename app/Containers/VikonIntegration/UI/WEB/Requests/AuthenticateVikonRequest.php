<?php

namespace App\Containers\VikonIntegration\UI\WEB\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AuthenticateVikonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:255'],
            'redirect_uri' => ['required', 'url'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Код авторизации обязателен.',
            'redirect_uri.required' => 'URL перенаправления обязателен.',
            'redirect_uri.url' => 'Некорректный URL перенаправления.',
        ];
    }
}
