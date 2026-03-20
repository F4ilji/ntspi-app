<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Article\Models\Post;
use App\Containers\Dashboard\Actions\PublishPostAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PublishPostController extends Controller
{
    public function __construct(
        private readonly PublishPostAction $publishPostAction,
    ) {}

    /**
     * Публикация черновика поста
     */
    public function __invoke(Request $request, Post $post): RedirectResponse
    {
        // Проверяем, что пост в статусе черновика (без publish_at)
        if ($post->publish_at) {
            return back()->with([
                'error' => 'Пост уже опубликован',
            ]);
        }

        try {
            $publishedPost = $this->publishPostAction->run($post);

            return back()->with([
                'success' => 'Новость успешно опубликована: ' . $publishedPost->title,
                'published_post_id' => $publishedPost->id,
            ]);
        } catch (\Exception $e) {
            return back()->with([
                'error' => 'Ошибка при публикации: ' . $e->getMessage(),
            ]);
        }
    }
}
