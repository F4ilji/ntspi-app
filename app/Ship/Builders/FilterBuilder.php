<?php

namespace App\Ship\Builders;

use RuntimeException;

class FilterBuilder
{
    protected array $filters = [];

    /**
     * Добавляет фильтр
     *
     * @param string $key Уникальный ключ фильтра (например 'category_filter')
     * @param string $type Тип фильтра ('category', 'tag', 'search', 'sort')
     * @param mixed $value Значение фильтра
     * @param string $param Параметр из URL (например 'category')
     * @param array|object|null $content Контент (например данные о категории)
     * @return $this
     */
    public function add(string $key, string $type, mixed $value, string $param, mixed $content = null): self
    {
        if (isset($this->filters[$key])) {
            throw new RuntimeException("Фильтр с ключом '$key' уже существует.");
        }

        $this->filters[$key] = [
            'type' => $type,
            'value' => $value,
            'param' => $param,
            'content' => $content,
        ];

        return $this;
    }

    /**
     * Возвращает собранные фильтры
     *
     * @return array
     */
    public function get(): array
    {
        return $this->filters;
    }

    public function reset(): self
    {
        $this->filters = [];
        return $this;
    }
}