<?php

namespace App\Containers\Widget\Tasks;

class GenerateWidgetFormValidationMessagesTask
{
    public function run(array $columns): array
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
                $messages[$name . '.unique'] = 'Поле должно быть уникальным';
            }
        }

        return $messages;
    }
}
