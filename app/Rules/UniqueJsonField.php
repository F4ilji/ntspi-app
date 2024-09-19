<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class UniqueJsonField implements Rule
{
    protected int $formId;
    protected string  $fieldName;

    public function __construct($formId, $fieldName)
    {
        $this->formId = $formId;
        $this->fieldName = $fieldName;
    }

    public function passes($attribute, $value)
    {
        // Получаем все записи для данного custom_form_id
        $responses = DB::table('custom_form_responses')
            ->where('custom_form_id', $this->formId)
            ->pluck('answers');

        // Проверяем, есть ли значение в JSON
        foreach ($responses as $response) {
            $answers = json_decode($response, true);
            if (isset($answers[$this->fieldName]) && $answers[$this->fieldName] === $value) {
                return false; // Значение не уникально
            }
        }

        return true; // Значение уникально
    }

    public function message()
    {
        return 'Поле должно быть уникальным';
    }
}
