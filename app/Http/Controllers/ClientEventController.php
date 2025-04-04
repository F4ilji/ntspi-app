<?php

namespace App\Http\Controllers;

use App\Enums\CacheKeys;
use App\Http\Resources\ClientBreadcrumbPage;
use App\Http\Resources\ClientBreadcrumbSection;
use App\Http\Resources\ClientBreadcrumbSubSection;
use App\Http\Resources\ClientEventCategoryResource;
use App\Http\Resources\ClientEventFullResource;
use App\Http\Resources\ClientEventResource;
use App\Http\Resources\ClientNavigationResource;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Page;
use App\Services\App\Breadcrumb\BreadcrumbService;
use App\Services\App\Seo\SeoPageProvider;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class ClientEventController extends Controller
{
    public function __construct(readonly SeoPageProvider $seoPageProvider){}

    public function index(Request $request): \Inertia\Response
    {
        $currentDate = $this->getCurrentDate($request);
        $cacheKey = md5(serialize([$currentDate, $request->all()]));

        $events = Cache::remember(
            CacheKeys::EVENTS_PREFIX->value . $cacheKey,
            now()->addHours(12),
            fn() => $this->getEvents($currentDate)
        );

        $eventDates = Cache::remember(
            CacheKeys::EVENTS_PREFIX->value . 'dates_' . $cacheKey,
            now()->addHours(12),
            fn() => $this->getEventDates($this->getFilters())
        );

        $categories = Cache::remember(
            CacheKeys::EVENTS_PREFIX->value . 'categories',
            now()->addDay(),
            fn() => ClientEventCategoryResource::collection(EventCategory::has('events')->get())
        );

        $filters = $this->getFilters();

        $seo = $this->seoPageProvider->getSeoForCurrentPage();

        return Inertia::render('Client/Events/Index', compact(
            'eventDates',
            'events',
            'currentDate',
            'filters',
            'categories',
            'seo'
        ));
    }

    public function show(string $slug): \Inertia\Response
    {
        $eventModel = Cache::remember(
            CacheKeys::EVENT_PREFIX->value . $slug,
            now()->addDay(),
            function () use ($slug) {
                return Event::where('slug', $slug)
                    ->with(['category', 'seo'])
                    ->firstOrFail();
            }
        );

        $seo = Cache::remember(
            CacheKeys::EVENT_PREFIX->value . 'seo_' . $slug,
            now()->addDay(),
            function () use ($eventModel) {
                return $this->seoPageProvider->getSeoForModel($eventModel);
            }
        );

        $event = new ClientEventFullResource($eventModel);


        return Inertia::render('Client/Events/Show', compact(
            'event',
            'seo'
        ));
    }

    public function archive(Request $request): \Inertia\Response
    {
        $cacheKey = md5(serialize($request->all()));

        $events = Cache::remember(
            CacheKeys::EVENTS_PREFIX->value . 'archive_' . $cacheKey,
            now()->addDay(),
            fn() => $this->getEventsArchive()
        );

        $categories = Cache::remember(
            CacheKeys::EVENTS_PREFIX->value . 'categories',
            now()->addDay(),
            fn() => ClientEventCategoryResource::collection(EventCategory::has('events')->get())
        );

        $filters = $this->getFilters();

        $seo = $this->seoPageProvider->getSeoForCurrentPage();

        return Inertia::render('Client/Events/Archive', compact(
            'events',
            'filters',
            'categories',
            'seo'
        ));
    }

    private function getCurrentDate(Request $request): array
    {
        $dateInput = $request->input('date');

        // Если дата введена, создаем объект Carbon, иначе получаем ближайшую дату события
        if ($dateInput) {
            $date = new Carbon($dateInput);
        } else {
            $nearestEvent = Event::whereDate('event_date_start', '>=', now())
                ->orderBy('event_date_start', 'asc')
                ->first();

            // Проверяем, найдено ли событие
            if ($nearestEvent) {
                $date = new Carbon($nearestEvent->event_date_start);
            } else {
                // Если событий нет, устанавливаем текущую дату
                $date = Carbon::now();
            }
        }

        return [
            'fullDate' => $date->format('Y-m-j'),
            'day' => $date->format('j'),
            'month' => $date->getTranslatedMonthName('Do MMMM'),
        ];
    }

    private function getEvents(array $currentDate)
    {
        return ClientEventResource::collection(Event::select('title', 'slug', 'event_date_start', 'event_time_start', 'address', 'is_online', 'category_id')
            ->whereDate('event_date_start', '=', $currentDate['fullDate'])
            ->with('category')
            ->when(request()->input('is_online'), function ($query, $value) {
                $this->applyOnlineFilter($query, $value);
            })
            ->when(request()->input('search'), function ($query, $search) {
                $query->whereRaw('LOWER(title) like ?', ["%".strtolower($search)."%"]);
            })
            ->when(request()->input('category'), function ($query) {
                $slugs = request()->input('category');
                if (is_array($slugs)) {
                    $query->whereHas('category', function ($query) use ($slugs) {
                        $query->whereIn('slug', $slugs);
                    });
                }
            })
            ->orderBy('event_time_start', 'asc')
            ->get());
    }

    private function getEventsArchive()
    {
        return ClientEventResource::collection(Event::select('title', 'slug', 'event_date_start', 'event_time_start', 'address', 'is_online', 'category_id')
            ->whereDate('event_date_start', '<', now())
            ->with('category')
            ->when(request()->input('is_online'), function ($query, $value) {
                $this->applyOnlineFilter($query, $value);
            })
            ->when(request()->input('search'), function ($query, $search) {
                $query->whereRaw('LOWER(title) like ?', ["%".strtolower($search)."%"]);
            })
            ->when(request()->input('category'), function ($query) {
                $slugs = request()->input('category');
                if (is_array($slugs)) {
                    $query->whereHas('category', function ($query) use ($slugs) {
                        $query->whereIn('slug', $slugs);
                    });
                }
            })
            ->when(request()->input('sort', 'desc'), function ($query, $sort) {
                $query->orderBy('event_date_start', $sort);
            })
            ->orderBy('event_time_start', 'asc')
            ->paginate(6)
            ->withQueryString());
    }

    private function getEventDates(array $filters): \Illuminate\Support\Collection
    {
        // Получаем события с учетом фильтров
        $events = Event::select('event_date_start')
            ->distinct()
            ->where('event_date_start', '>=', date('Y-m-d'))
            ->when($filters['is_online_filter']['value'], function ($query, $value) {
                if ($value === 'online') {
                    $query->where('is_online', true);
                } elseif ($value === 'offline') {
                    $query->where('is_online', false);
                }
            })
            ->when($filters['category_filter']['value'], function ($query) {
                $slugs = request()->input('category');
                if (is_array($slugs)) {
                    $query->whereHas('category', function ($query) use ($slugs) {
                        $query->whereIn('slug', $slugs);
                    });
                }
            })
            ->orderBy('event_date_start')
            ->get();

        // Получаем массив без ключей



        // Извлекаем уникальные даты из событий
        return $events->map(function ($event) {
            $date = new DateTime($event->event_date_start);
            return [
                'day' => $date->format('j'),
                'dayOfWeek' => $this->getDayOfWeekRussian($date->format('l')),
                'date' => $date->format('Y-m-j')
            ];
        })
            ->groupBy(function ($item) {
                return (new DateTime($item['date']))->format('m');
            })
            ->map(function ($group, $month) {
                return [
                    "month" => $this->getMonthNameRussian((int)$month),
                    "events" => $group->toArray()
                ];
            })
            ->sortKeys() // Сортируем ключи по возрастанию
            ->values();
    }

    private function getFilters(): array
    {
        $categoriesContent = [];
        if (request()->input('category')) {
            foreach (request()->input('category') as $item) {
                $categoriesContent[$item] = new ClientEventCategoryResource(EventCategory::where('slug', $item)->first());
            }
        }

        return [
            'search_filter' => [
                'type' => 'search',
                'value' => request()->input('search'),
                'param' => 'search'
            ],
            'category_filter' => [
                'type' => 'category',
                'value' => request()->input('category'),
                'param' => 'category',
                'content' => $categoriesContent,
            ],
            'sortingBy_filter' => [
                'type' => 'sort',
                'value' => request()->input('sort'),
                'param' => 'sort',
            ],
            'is_online_filter' => [
                'type' => 'is_online',
                'value' => request()->input('is_online'),
                'param' => 'is_online',
            ],
        ];
    }

    private function getDayOfWeekRussian(string $dayOfWeek): string
    {
        $russianDays = [
            'Monday' => 'пн',
            'Tuesday' => 'вт',
            'Wednesday' => 'ср',
            'Thursday' => 'чт',
            'Friday' => 'пт',
            'Saturday' => 'сб',
            'Sunday' => 'вс'
        ];

        return $russianDays[$dayOfWeek] ?? '';
    }

    private function getMonthNameRussian(int $month): string
    {
        $russianMonths = [
            1 => 'Январь',
            2 => 'Февраль',
            3 => 'Март',
            4 => 'Апрель',
            5 => 'Май',
            6 => 'Июнь',
            7 => 'Июль',
            8 => 'Август',
            9 => 'Сентябрь',
            10 => 'Октябрь',
            11 => 'Ноябрь',
            12 => 'Декабрь'
        ];

        return $russianMonths[$month] ?? '';
    }

    private function applyOnlineFilter($query, $isOnline): void
    {
        if ($isOnline === 'online') {
            $query->where('is_online', true);
        } elseif ($isOnline === 'offline') {
            $query->where('is_online', false);
        }
    }
}