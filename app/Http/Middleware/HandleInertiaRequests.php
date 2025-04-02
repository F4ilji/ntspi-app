<?php

namespace App\Http\Middleware;

use App\Http\Resources\ClientNavigationResource;
use App\Models\MainSection;
use App\Services\App\Breadcrumb\BreadcrumbService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Middleware;
use Tightenco\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        // Навигация (кешированная)
        $navigation = Cache::remember('navigation', now()->addHours(1), function () {
            return ClientNavigationResource::collection(
                MainSection::with('subSections.pages.section')
                    ->orderBy('sort', 'asc')
                    ->get()
            );
        });

        // Хлебные крошки (автоматически по текущему URL)
        $breadcrumbs = app(BreadcrumbService::class)->generateBreadcrumbs();

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