<?php

namespace App\Containers\Dashboard\Tasks;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class CallAiServiceTask
{
    /**
     * Отправляет текст в AI сервис для извлечения структурированных данных
     *
     * @param string $text Исходный текст новости
     * @param Collection $categories Коллекция категорий для выбора
     * @return array|null Распознанные данные (title, body, authors, tags, category_id)
     */
    public function run(string $text, Collection $categories): ?array
    {
        $categoryList = collect($categories)
            ->map(fn($cat) => "{$cat->id}: {$cat->title}")
            ->implode("\n");

        $systemPrompt = $this->buildSystemPrompt($categoryList);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('QWEN_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://routerai.ru/api/v1/chat/completions', [
            'model' => 'qwen/qwen3.5-flash-02-23',
            'reasoning' => ['enabled' => false],
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $text],
            ],
            'response_format' => ['type' => 'json_object'],
            'temperature' => 0,
            'max_tokens' => 2000,
        ]);

        if ($response->successful()) {
            $result = $response->json();
            return json_decode($result['choices'][0]['message']['content'], true);
        }

        return null;
    }

    /**
     * Формирует системный промпт для AI
     */
    private function buildSystemPrompt(string $categoryList): string
    {
        return "Ты — строгий агент по извлечению данных. Твоя задача — преобразовать входящий текст новости в JSON.\n\n"
            . "СПИСОК КАТЕГОРИЙ (ID: Название):\n"
            . $categoryList
            . "\n\nПРАВИЛА:\n"
            . "1. Поля \"title\" и \"body\" копируй ТОЧЬ-В-ТОЧЬ из источника. Запрещено исправлять ошибки, менять пунктуацию или добавлять Markdown-разметку. Только чистый текст.\n"
            . "2. В конце новости извлеки имя автора(-ов) и их должности, это должен быть массив \"authors\". Если оно не указано явно — верни null.\n"
            . "3. \"tags\" извлекай ТОЛЬКО слова, начинающиеся с символа решетки (#). Если таких слов в тексте нет — верни null. Не создавай свои теги.\n"
            . "4. В поле \"category_id\" запиши идентификатор из предоставленного списка выше.\n"
            . "5. Полностью игнорируй комментарии пользователей и рекламный мусор, не включая их в \"body\".\n\n"
            . "ОТВЕТ ДОЛЖЕН БЫТЬ СТРОГО В JSON:\n"
            . "{\n"
            . '  "title": "string",' . "\n"
            . '  "body": "string",' . "\n"
            . '  "authors": ["ФИО и должность"] или null,' . "\n"
            . '  "tags": ["#тег1"] или null,' . "\n"
            . '  "category_id": number/string' . "\n"
            . "}";
    }
}
