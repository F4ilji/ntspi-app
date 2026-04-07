<?php

namespace App\Containers\Dashboard\UI\WEB\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttachDepartmentWorkerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'position' => ['required', 'string', 'max:255'],
            'service_email' => ['nullable', 'email', 'max:255'],
            'service_phone' => ['nullable', 'string', 'max:20'],
            'cabinet' => ['nullable', 'string', 'max:10'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Необходимо выбрать сотрудника',
            'user_id.exists' => 'Выбранный сотрудник не существует',
            'position.required' => 'Должность обязательна для заполнения',
            'position.max' => 'Должность не должна превышать 255 символов',
            'service_email.email' => 'Введите корректный email адрес',
        ];
    }
}
