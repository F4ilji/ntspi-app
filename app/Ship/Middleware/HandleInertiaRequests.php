<?php

namespace App\Ship\Middleware;

use App\Containers\AppStructure\Tasks\GetCachedNavigationDataTask;
use App\Containers\AppStructure\Tasks\GenerateBreadcrumbsTask;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tightenco\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function __construct(private readonly GenerateBreadcrumbsTask $generateBreadcrumbsTask){}

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        // Навигация (кешированная)
        $navigation = app(GetCachedNavigationDataTask::class)->run();

        // Хлебные крошки (автоматически по текущему URL)
        $breadcrumbs = $this->generateBreadcrumbsTask->run();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? $request->user()->only('id', 'name', 'email', 'created_at') : null,
            ],
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'navigation' => $navigation,
            'breadcrumbs' => $breadcrumbs, // Добавляем хлебные крошки
            'urlPrev' => function () {
                if (url()->previous() !== url()->current()) {
                    return url()->previous();
                }
                return 'empty';
            },
        ];
    }
}
