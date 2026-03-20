<?php

namespace App\Containers\Dashboard\Tasks;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CallAiServiceForFileSelectionTask
{
    /**
     * Отправляет фрагменты файлов в AI для определения основного файла
     *
     * @param array $fragments Массив фрагментов [['filename' => '...', 'text' => '...'], ...]
     * @return array|null Ответ AI с названием основного файла
     */
    public function run(array $fragments): ?array
    {
        Log::info('[CallAiServiceForFileSelectionTask] Начало LLM запроса', [
            'fragments_count' => count($fragments),
        ]);

        $prompt = $this->buildPrompt($fragments);

        Log::info('[CallAiServiceForFileSelectionTask] Отправка запроса к AI');

        try {
            $response = Http::timeout(30) // Таймаут 30 секунд
                ->withHeaders([
                    'Authorization' => 'Bearer ' . env('QWEN_API_KEY'),
                    'Content-Type' => 'application/json',
                ])->post('https://routerai.ru/api/v1/chat/completions', [
                    'model' => 'qwen/qwen3.5-flash-02-23',
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'response_format' => ['type' => 'json_object'],
                    'temperature' => 0,
                    'max_tokens' => 500,
                ]);

            Log::info('[CallAiServiceForFileSelectionTask] Ответ от AI', [
                'status' => $response->status(),
                'successful' => $response->successful(),
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $llmResponse = json_decode($result['choices'][0]['message']['content'], true);
                
                Log::info('[CallAiServiceForFileSelectionTask] Распознан ответ', [
                    'main_file' => $llmResponse['main_file'] ?? null,
                ]);
                
                return $llmResponse;
            } else {
                Log::error('[CallAiServiceForFileSelectionTask] Ошибка AI запроса', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('[CallAiServiceForFileSelectionTask] Исключение при запросе к AI', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return null;
    }

    /**
     * Формирует промпт для AI
     */
    private function buildPrompt(array $fragments): string
    {
        $filesInfo = "";
        foreach ($fragments as $index => $fragment) {
            $filesInfo .= "Файл #{$index}: {$fragment['filename']}\n";
            $filesInfo .= "Текст: {$fragment['text']}\n\n";
        }

        return "Ты — ассистент для определения основного файла с новостью. " .
            "Тебе предоставлены фрагменты текста из нескольких DOCX файлов.\n\n" .
            "СПИСОК ФАЙЛОВ:\n{$filesInfo}\n" .
            "ЗАДАЧА: Определи, какой файл содержит основной текст новости (не приложение, не документ, а именно новость/статью).\n\n" .
            "ВЕРНИ СТРОГО JSON:\n" .
            "{\n" .
            '  "main_file": "точное название файла с расширением .docx",' . "\n" .
            '  "reason": "краткое объяснение выбора"' . "\n" .
            "}\n\n" .
            "Если не можешь определить — верни первый файл из списка.";
    }
}
