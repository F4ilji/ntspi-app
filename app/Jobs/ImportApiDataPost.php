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
            $response = Http::get('https://crawdad-fresh-bream.ngrok-free.app/api/posts')->object();

            foreach ($response as $item) {
                try {
                    $postResponse = Http::get('https://crawdad-fresh-bream.ngrok-free.app/api/posts/' . $item->ID)->object();

                    // Проверка на наличие данных
                    if (!isset($postResponse->post)) {
                        Log::error('Post data not found for ID: ' . $item->ID);
                        continue;
                    }

                    $postData = $postResponse->post;
                    $images = $postResponse->images ?? []; // Обработка отсутствия изображений

                    // Обработка изображения
                    $imagePaths = [];
                    foreach ($images as $image) {
                        $imagePaths[] = 'upload/' . $image->SUBDIR . '/' . $image->FILE_NAME;
                    }

                    $content = strip_tags($postData->DETAIL_TEXT, '<a>');
                    $contentData = [
                        [
                            'type' => 'paragraph',
                            'data' => [
                                'content' => $content,
                            ],
                        ],
                    ];

                    $slug = $this->generateSlug($postData->NAME);

                    // Создание нового поста
                    Post::create([
                        'id' => $postData->ID,
                        'title' => $postData->NAME,
                        'slug' => $slug,
                        'preview_text' => strip_tags($postData->PREVIEW_TEXT),
                        'authors' => array('Без автора'),
                        'content' => $contentData, // Преобразуем в JSON
                        'status' => 'published',
                        'images' => $imagePaths,
                        'search_data' => $content, // Преобразуем в JSON
                        'reading_time' => 2,
                        'user_id' => 1,
                        'publish_at' => $postData->DATE_CREATE,
                        'created_at' => $postData->DATE_CREATE,
                        'updated_at' => $postData->DATE_CREATE,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error importing post ID: ' . $item->ID . ' - ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::error('Error fetching posts: ' . $e->getMessage());
        }
    }

    private function generateSlug($name)
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

}
