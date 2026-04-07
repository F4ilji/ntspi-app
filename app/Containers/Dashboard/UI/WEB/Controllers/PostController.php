<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Article\Enums\PostStatus;
use App\Containers\Article\Models\Post;
use App\Containers\Dashboard\Actions\Posts\BulkDeletePostsAction;
use App\Containers\Dashboard\Actions\Posts\BulkPublishPostsAction;
use App\Containers\Dashboard\Actions\Posts\BulkVerificationPostsAction;
use App\Containers\Dashboard\Actions\Posts\CreatePostAction;
use App\Containers\Dashboard\Actions\Posts\DeletePostAction;
use App\Containers\Dashboard\Actions\Posts\GetPostFormDataAction;
use App\Containers\Dashboard\Actions\Posts\ListAiPreparedPostsAction;
use App\Containers\Dashboard\Actions\Posts\ListPostsAction;
use App\Containers\Dashboard\Actions\Posts\UpdatePostAction;
use App\Containers\Dashboard\UI\WEB\Requests\StorePostRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostController extends Controller
{
    public function __construct(
        private readonly CreatePostAction $createPostAction,
        private readonly UpdatePostAction $updatePostAction,
        private readonly DeletePostAction $deletePostAction,
        private readonly ListPostsAction $listPostsAction,
        private readonly ListAiPreparedPostsAction $listAiPreparedPostsAction,
        private readonly GetPostFormDataAction $getPostFormDataAction,
        private readonly BulkDeletePostsAction $bulkDeletePostsAction,
        private readonly BulkPublishPostsAction $bulkPublishPostsAction,
        private readonly BulkVerificationPostsAction $bulkVerificationPostsAction,
    ) {}

    /**
     * Показывает форму создания поста
     */
    public function create(Request $request): \Inertia\Response
    {
        $formData = $this->getPostFormDataAction->run();

        return Inertia::render('Dashboard/Posts/Create', $formData);
    }

    /**
     * Создает новый пост
     */
    public function store(StorePostRequest $request): RedirectResponse
    {
        try {
            $this->createPostAction->run($request->validated());

            return redirect()->route('dashboard.posts.index')
                ->with('success', 'Новость успешно создана!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании новости: ' . $e->getMessage());
        }
    }

    /**
     * Показывает форму редактирования поста
     */
    public function edit(Post $post): \Inertia\Response
    {
        $post->load(['category', 'seo']);
        $formData = $this->getPostFormDataAction->run();

        return Inertia::render('Dashboard/Posts/Edit', [
            'post' => [
                ...$post->toArray(),
                'publish_setting' => [
                    'publish_after' => $post->publish_at !== null,
                    'publish_at' => $post->publish_at,
                ],
                'publication' => [
                    'vk' => true,
                    'telegram' => true,
                ],
            ],
            ...$formData,
        ]);
    }

    /**
     * Обновляет существующий пост
     */
    public function update(StorePostRequest $request, Post $post): RedirectResponse
    {
        try {
            $this->updatePostAction->run($post, $request->validated());

            return redirect()->route('dashboard.posts.index')
                ->with('success', 'Новость успешно обновлена!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении новости: ' . $e->getMessage());
        }
    }

    /**
     * Показывает список постов
     */
    public function index(Request $request): \Inertia\Response
    {
        $filters = [
            'status' => $request->status,
            'search' => $request->search,
        ];

        $posts = $this->listPostsAction->run($filters, 20);

        return Inertia::render('Dashboard/Posts/Index', [
            'posts' => $posts->withQueryString(),
            'filters' => $filters,
        ]);
    }

    /**
     * Показывает AI подготовленные посты для модерации
     */
    public function aiPrepared(Request $request): \Inertia\Response
    {
        $aiPreparedPosts = $this->listAiPreparedPostsAction->run();

        return Inertia::render('Dashboard/Posts/AiPrepared', [
            'aiPreparedPosts' => $aiPreparedPosts,
        ]);
    }

    /**
     * Показывает просмотр поста
     */
    public function show(Post $post): \Inertia\Response
    {
        $post->load(['category', 'author', 'slide', 'tags']);

        return Inertia::render('Dashboard/Posts/Show', [
            'post' => [
                ...$post->toArray(),
                'slide' => $post->slide?->toArray(),
            ],
        ]);
    }

    /**
     * Удаляет пост
     */
    public function destroy(Post $post): RedirectResponse
    {
        try {
            $this->deletePostAction->run($post);

            return redirect()->route('dashboard.posts.index')
                ->with('success', 'Новость успешно удалена!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Ошибка при удалении новости: ' . $e->getMessage());
        }
    }

    /**
     * Массовое удаление постов
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer|exists:posts,id']);

        try {
            $count = $this->bulkDeletePostsAction->run($request->ids);

            return redirect()->back()
                ->with('success', "Удалено {$count} новостей");
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Ошибка при удалении: ' . $e->getMessage());
        }
    }

    /**
     * Массовая публикация постов
     */
    public function bulkPublish(Request $request): RedirectResponse
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer|exists:posts,id']);

        try {
            $count = $this->bulkPublishPostsAction->run($request->ids);

            return redirect()->back()
                ->with('success', "Опубликовано {$count} новостей");
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Ошибка при публикации: ' . $e->getMessage());
        }
    }

    /**
     * Массовая установка статуса "На модерации"
     */
    public function bulkVerification(Request $request): RedirectResponse
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer|exists:posts,id']);

        try {
            $count = $this->bulkVerificationPostsAction->run($request->ids);

            return redirect()->back()
                ->with('success', "{$count} новостей переведено на модерацию");
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Ошибка: ' . $e->getMessage());
        }
    }
}
