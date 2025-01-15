<?php

namespace App\Http\Controllers;

use App\Enums\CustomFormStatus;
use App\Http\Requests\FormResponseRequest;
use App\Http\Resources\ClientFormResource;
use App\Models\CustomForm;
use App\Models\CustomFormResponse;
use App\Rules\UniqueJsonField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientWidgetFormController extends Controller
{
    public function single(string $id)
    {
        return new ClientFormResource(CustomForm::query()->where('status', CustomFormStatus::PUBLISHED)->where('form_id', $id)->first());
    }

    public function submit(int $id, Request $request)
    {
        $data = CustomForm::findOrFail($id);


        $rules = $this->generateValidationRules($data['columns'], $data['id']);


        $messages = $this->generateValidationMessages($data['columns']);


        $validateData = Validator::make($request->all(), $rules, $messages);


        if ($validateData->fails()) {
            return response()->json($validateData->errors(), 422);
        }

        $this->storeResponse($id, $validateData->validated());


        return response()->json([
            'message' => $data['send_message'],
            'status' => 'ok'
        ]);
    }

    private function generateValidationRules(array $columns, int $id): array
    {
        $rules = [];

        foreach ($columns as $column) {
            // Извлекаем информацию о поле
            $name = $column['data']['name_field'];
            $rulesForField = [];

            // Проверяем, является ли поле массивом (множественный выбор)
            if ($column['type'] === 'multiple_choice') {
                $rulesForField[] = 'required'; // Обязательно для множественного выбора
                $rulesForField[] = 'array'; // Указывает, что это массив
                $rulesForField[] = 'min:1'; // Минимум один элемент
                $rules[$name] = $rulesForField; // Правила для каждого элемента массива
            } else {
                // Обработка обычных полей
                if (!empty($column['data']['rules']['required'])) {
                    $rulesForField[] = 'required';
                }

                if (!empty($column['data']['rules']['min'])) {
                    $rulesForField[] = 'min:' . $column['data']['rules']['min'];
                }

                if (!empty($column['data']['rules']['max'])) {
                    $rulesForField[] = 'max:' . $column['data']['rules']['max'];
                }

                if (!empty($column['data']['rules']['unique']) && $column['data']['rules']['unique'] === true) {
                    $rulesForField[] = new UniqueJsonField($id, $name);
                }

                $rules[$name] = $rulesForField; // Добавляем правила для обычного поля
            }
        }

        return $rules;
    }

    private function generateValidationMessages(array $columns): array
    {
        $messages = [];

        foreach ($columns as $column) {
            $name = $column['data']['name_field'];

            if (!empty($column['data']['rules']['required'])) {
                $messages[$name . '.required'] = 'Поле обязательно для заполнения.';
            }

            if (!empty($column['data']['rules']['min'])) {
                $messages[$name . '.min'] = 'Минимальная длина поля должна быть ' . $column['data']['rules']['min'] . ' символов.';
            }

            if (!empty($column['data']['rules']['max'])) {
                $messages[$name . '.max'] = 'Максимальная длина поля должна быть ' . $column['data']['rules']['max'] . ' символов.';
            }

            if (!empty($column['data']['rules']['unique'])) {
                $messages[$name . '.max'] = 'Поле должно быть уникальным';
            }
        }

        return $messages;
    }

    private function storeResponse(int $id, array $validateData): void
    {
        CustomFormResponse::create([
            'custom_form_id' => $id,
            'answers' => $validateData,
        ]);
    }
}
