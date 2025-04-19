<?php

namespace App\Jobs;

use App\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImportApiDataPost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            // Получаем первую страницу данных
            $response = Http::get(env('TRANSFER_PROXY_URL') . '/api/posts')->object();
            $last_page = $response->last_page;
            Log::info('Last page: ' . $last_page);

            // Проходим по всем страницам
            for ($page = 1; $page <= $last_page; $page++) {
                $response = Http::get(env('TRANSFER_PROXY_URL') . "/api/posts?page=$page")->object();
                $results = $response->data;

                // Обрабатываем каждую запись
                foreach ($results as $post) {
                    Log::info('Processing post ID: ' . $post->ID);

                    try {
                        // Получаем массив изображений
                        $images = $post->images ?? []; // Обработка отсутствия изображений
                        $imagePaths = [];

                        // Проверяем, есть ли изображения
                        if (empty($images)) {
                            Log::warning('No images found for post ID: ' . $post->ID);
                        } else {
                            // Проходимся по массиву изображений
                            foreach ($images as $image) {
                                // Проверяем наличие необходимых свойств
                                if (isset($image->SUBDIR) && isset($image->FILE_NAME)) {
                                    $imagePaths[] = 'upload/' . $image->SUBDIR . '/' . $image->FILE_NAME;
                                } else {
                                    Log::warning('Image data is incomplete for post ID: ' . $post->ID);
                                }
                            }
                        }

                        // Логируем пути к изображениям для отладки
                        Log::info('Image paths for post ID ' . $post->ID . ': ' . json_encode($imagePaths));

                        // Обработка контента поста
                        $content = strip_tags($post->DETAIL_TEXT, '<a>');

                        $readingTime = $this->calculateReadingTime($content);
                        $contentData = [
                            [
                                'type' => 'paragraph',
                                'data' => [
                                    'content' => $content,
                                ],
                            ],
                        ];

                        // Генерация уникального slug
                        $slug = $this->generateSlug($post->NAME);

                        // Создание нового поста
                        Post::firstOrCreate(
                            ['id' => $post->ID], // Условие поиска по ID
                            [
                                'title' => $post->NAME,
                                'slug' => $slug,
                                'preview_text' => strip_tags($post->PREVIEW_TEXT),
                                'authors' => ['Без автора'],
                                'content' => $contentData, // Преобразуем в JSON
                                'status' => 'published',
                                'images' => $imagePaths, // Сохраняем пути к изображениям
                                'preview' => $imagePaths[0],
                                'search_data' => strip_tags($content), // Преобразуем в JSON
                                'reading_time' => $readingTime,
                                'user_id' => 1,
                                'publish_at' => $post->DATE_CREATE,
                                'created_at' => $post->DATE_CREATE,
                                'updated_at' => $post->DATE_CREATE,
                            ]
                        );
                    } catch (\Exception $e) {
                        Log::error('Error importing post ID: ' . $post->ID . ' - ' . $e->getMessage());
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error fetching posts: ' . $e->getMessage());
        }
    }

    private function generateSlug($name): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (Post::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    private function calculateReadingTime(string $text): int
    {
        $wordCount = str_word_count($text, 0, "АаБбВвГгДдЕеЁёЖжЗзИиЙйКкЛлМмНнОоПпРрСсТтУуФфХхЦцЧчШшЩщЪъЫыЬьЭэЮюЯя");
        $wordsPerMinute = 120; // Средняя скорость чтения
        return max(1, round($wordCount / $wordsPerMinute));
    }

}
