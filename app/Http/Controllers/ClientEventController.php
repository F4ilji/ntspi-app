<?php

namespace App\Http\Controllers;

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
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientEventController extends Controller
{
    public function index(Request $request)
    {
        $currentDate = $this->getCurrentDate($request);
        $filters = $this->getFilters();
        $eventDates = $this->getEventDates($filters); // Передаем фильтры

        $events = $this->getEvents($currentDate);
        $categories = ClientEventCategoryResource::collection(EventCategory::has('events')->get());

        $routeUrl = route('client.event.index');
        $path = ltrim(parse_url($routeUrl, PHP_URL_PATH), '/');

        $page = Page::where('path', '=', $path)->with('section.pages.section', 'section.mainSection')->first();

        if (isset($page->section)) {
            $breadcrumbs = [
                'mainSection' => new ClientBreadcrumbSection($page->section->mainSection),
                'subSection' => new ClientBreadcrumbSubSection($page->section),
                'page' => new ClientBreadcrumbPage($page),
            ];
        } else {
            $breadcrumbs = null;
        }

        return Inertia::render('Client/Events/Index', compact('eventDates', 'events', 'currentDate', 'filters', 'categories', 'breadcrumbs'));
    }

    public function show(string $slug)
    {
        $event = new ClientEventFullResource(Event::where('slug', '=', $slug)->with('category')->first());

        $routeUrl = route('client.event.index');
        $path = ltrim(parse_url($routeUrl, PHP_URL_PATH), '/');

        $page = Page::where('path', '=', $path)->with('section.pages.section', 'section.mainSection')->first();

        if (isset($page->section)) {
            $breadcrumbs = [
                'mainSection' => new ClientBreadcrumbSection($page->section->mainSection),
                'subSection' => new ClientBreadcrumbSubSection($page->section),
                'page' => new ClientBreadcrumbPage($page),
            ];
        } else {
            $breadcrumbs = null;
        }

        return Inertia::render('Client/Events/Show', compact('event', 'breadcrumbs'));
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

        $mappingDates = $events->map(function ($event) {
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
            ->values(); // Получаем массив без ключей

        // Извлекаем уникальные даты из событий
        return $mappingDates;
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

    private function applyOnlineFilter($query, $isOnline)
    {
        if ($isOnline === 'online') {
            $query->where('is_online', true);
        } elseif ($isOnline === 'offline') {
            $query->where('is_online', false);
        }
    }
}