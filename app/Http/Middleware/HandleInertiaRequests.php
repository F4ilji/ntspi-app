<?php

namespace App\Http\Middleware;

use App\Containers\AppStructure\Models\MainSection;
use App\Containers\AppStructure\UI\API\Transformers\NavigationResource;
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
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? $request->user()->only('id', 'name', 'email', 'created_at') : null,
            ],
            'ziggy' => function() {
                return array_merge((new Ziggy())->toArray(), [
                    'location' => url()->current(),
                ]);
            },
            'navigation' => $this->getNavigation(),
            'breadcrumbs' => $this->getBreadcrumbs(),
            'urlPrev' => function () {
                if (url()->previous() !== url()->current()) {
                    return url()->previous();
                }
                return 'empty';
            },
        ];
    }

    private function getNavigation()
    {
        return Cache::remember('navigation', now()->addHours(1), function () {
            return NavigationResource::collection(
                MainSection::with('subSections.pages.section')
                    ->orderBy('sort', 'asc')
                    ->get()
            );
        });
    }

    private function getBreadcrumbs()
    {
        return app(BreadcrumbService::class)->generateBreadcrumbs();
    }
}