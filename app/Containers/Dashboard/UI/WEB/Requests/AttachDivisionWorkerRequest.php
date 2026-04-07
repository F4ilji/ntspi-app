<?php

namespace App\Containers\Dashboard\UI\WEB\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttachDivisionWorkerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'administrativePosition' => ['required', 'string', 'max:255'],
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
            'administrativePosition.required' => 'Административная должность обязательна для заполнения',
            'administrativePosition.max' => 'Должность не должна превышать 255 символов',
            'service_email.email' => 'Некорректный email адрес',
        ];
    }
}
