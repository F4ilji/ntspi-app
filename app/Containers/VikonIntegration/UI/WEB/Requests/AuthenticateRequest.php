<?php

namespace App\Containers\VikonIntegration\UI\WEB\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthenticateRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:255'],
            'redirect_uri' => ['required', 'url'],
        ];
    }
}
