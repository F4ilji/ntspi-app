<?php

namespace App\Jobs;

use App\Services\VK\VkService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class CreateVkPost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        readonly private string $title,
        readonly private string $text,
        readonly private array $images = [],
        readonly private int $post_id,
        readonly private int|null $publish_date = null,

    )
    {}

    public function handle()
    {
        try {
            $vkService = new VkService();
            $vk_post = $vkService->createPost($this->title, $this->text, $this->images, $this->publish_date);
            DB::table('posts_vk_posts')->insert(
                [
                    'post_id' => $this->post_id,
                    'vk_post_id' => $vk_post['post_id'],
                    'unchange_time_after' => Carbon::now()->addWeek(),
                ]
            );

        } catch (\Exception $e) {
            Log::error('Ошибка при создании поста: ' . $e->getMessage());
            throw $e; // Перебрасываем исключение для повторной попытки
        }
    }

}
