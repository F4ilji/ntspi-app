<?php

namespace App\Containers\Dashboard\Tasks;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
        try {
            Log::info('[CallAiServiceTask] Начало AI запроса', [
                'text_length' => strlen($text),
                'categories_count' => $categories->count(),
            ]);

            // Проверяем наличие API ключа
            $apiKey = env('QWEN_API_KEY');
            if (empty($apiKey)) {
                Log::error('[CallAiServiceTask] API ключ не настроен', [
                    'env_key' => 'QWEN_API_KEY',
                ]);
                throw new \RuntimeException('AI API ключ не настроен в окружении');
            }

            $categoryList = collect($categories)
                ->map(fn($cat) => "{$cat->id}: {$cat->title}")
                ->implode("\n");

            $systemPrompt = $this->buildSystemPrompt($categoryList);

            Log::info('[CallAiServiceTask] Отправка запроса к AI', [
                'url' => 'https://routerai.ru/api/v1/chat/completions',
                'model' => 'qwen/qwen3.5-flash-02-23',
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])
                ->timeout(60) // Увеличиваем timeout до 60 секунд
                ->retry(3, 1000, function ($exception, $response) {
                    // Повторяем только при ошибках соединения или 5xx
                    if ($response && $response->successful()) {
                        return false;
                    }
                    Log::warning('[CallAiServiceTask] Попытка не удалась, повтор...', [
                        'exception' => $exception->getMessage(),
                    ]);
                    return true;
                })
                ->post('https://routerai.ru/api/v1/chat/completions', [
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

            Log::info('[CallAiServiceTask] Ответ от AI', [
                'status' => $response->status(),
                'successful' => $response->successful(),
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $content = $result['choices'][0]['message']['content'] ?? null;

                if (empty($content)) {
                    Log::error('[CallAiServiceTask] Пустой ответ от AI', [
                        'response' => $result,
                    ]);
                    return null;
                }

                $data = json_decode($content, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    Log::error('[CallAiServiceTask] Ошибка парсинга JSON ответа', [
                        'error' => json_last_error_msg(),
                        'content' => substr($content, 0, 500),
                    ]);
                    return null;
                }

                Log::info('[CallAiServiceTask] AI данные успешно распознаны', [
                    'title' => $data['title'] ?? 'N/A',
                    'has_body' => isset($data['body']),
                    'category_id' => $data['category_id'] ?? 'N/A',
                ]);

                return $data;
            }

            Log::error('[CallAiServiceTask] AI запрос не удался', [
                'status' => $response->status(),
                'body' => $response->body(),
                'headers' => $response->headers(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('[CallAiServiceTask] Критическая ошибка AI запроса', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
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
