<?php

namespace App\Containers\Widget\Tasks;

use App\Containers\Widget\Data\Rules\UniqueJsonField;
use Illuminate\Support\Collection;

class GenerateWidgetFormValidationRulesTask
{
    /**
     * Генерирует массив правил валидации для полей формы на основе их конфигурации.
     *
     * @param array $columns Конфигурация полей
     * @param int $formId ID формы для правила 'unique'
     * @return array Массив правил валидации в формате Laravel
     */
    public function run(array $columns, int $formId): array
    {
        return collect($columns)
            ->mapWithKeys(fn(array $column) => [
                // Ключом будет название поля, значением - массив его правил
                $column['data']['name_field'] => $this->resolveRulesForColumn($column, $formId)
            ])
            ->all();
    }

    /**
     * Определяет и делегирует создание правил в зависимости от типа поля.
     */
    private function resolveRulesForColumn(array $column, int $formId): array
    {
        $type = $column['type'] ?? 'default';

        return match ($type) {
            'multiple_choice' => ['required', 'array', 'min:1'],
            default => $this->buildRulesFromConfig($column, $formId),
        };
    }

    /**
     * Собирает стандартный набор правил валидации из конфигурации поля.
     */
    private function buildRulesFromConfig(array $column, int $formId): array
    {
        $configRules = $column['data']['rules'] ?? [];
        $validationRules = [];

        // Правила, которые просто включаются по флагу true
        if (!empty($configRules['required'])) {
            $validationRules[] = 'required';
        }

        // Правила, требующие значения (min, max, и т.д.)
        foreach (['min', 'max'] as $rule) {
            // Используем isset для корректной обработки значения '0'
            if (isset($configRules[$rule])) {
                $validationRules[] = "{$rule}:{$configRules[$rule]}";
            }
        }

        // Сложные правила, требующие инстанцирования объектов
        if (!empty($configRules['unique'])) {
            $fieldName = $column['data']['name_field'];
            $validationRules[] = new UniqueJsonField($formId, $fieldName);
        }

        return $validationRules;
    }
}
