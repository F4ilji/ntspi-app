<?php

namespace App\Containers\Search\UI\API\Controllers;

use App\Containers\AdditionalEducation\Models\AdditionalEducation;
use App\Containers\AppStructure\Models\Page;
use App\Containers\Article\Models\Post;
use App\Containers\Education\Models\EducationalProgram;
use App\Containers\Event\Models\Event;
use App\Containers\InstituteStructure\Models\Faculty;
use App\Containers\Schedule\Models\EducationalGroup;
use App\Containers\Search\UI\API\Transformers\AdditionalEducationSearchResource;
use App\Containers\Search\UI\API\Transformers\EducationalProgramSearchResource;
use App\Containers\Search\UI\API\Transformers\EducationGroupSearchResource;
use App\Containers\Search\UI\API\Transformers\EventSearchResource;
use App\Containers\Search\UI\API\Transformers\FacultySearchResource;
use App\Containers\Search\UI\API\Transformers\PageSearchResource;
use App\Containers\Search\UI\API\Transformers\PostSearchResource;
use App\Containers\Search\UI\API\Transformers\UserSearchResource;
use App\Containers\User\Models\User;
use App\Ship\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use ProtoneMedia\LaravelCrossEloquentSearch\Search;

class SearchController extends Controller
{
    private array $resourceMap = [
        Post::class => PostSearchResource::class,
        Page::class => PageSearchResource::class,
        EducationalGroup::class => EducationGroupSearchResource::class,
        EducationalProgram::class => EducationalProgramSearchResource::class,
        Event::class => EventSearchResource::class,
        AdditionalEducation::class => AdditionalEducationSearchResource::class, //??
        User::class => UserSearchResource::class, //??
        Faculty::class => FacultySearchResource::class, //??
    ];
    public function index(Request $request)
    {
        $req = Str::lower($request->query('search'));
        if (!$req) {
            return response()->json([
                'searchRes' => null,
            ]);
        }


        $results = Search::new()
            ->add(Post::where('status', '=', 'published'), ['title', 'search_data'], 'publish_at')
            ->add(Page::with('section')->where('searchable', '=', true), ['title', 'search_data'])
            ->add(Event::where('event_date_start', '>', Date::now()), 'title', 'created_at')
            ->add(AdditionalEducation::where('is_active', '=', true), 'title')
            ->add(EducationalGroup::with('schedules'), 'title')
            ->add(EducationalProgram::where('status', '=', true)->whereHas('admission_plans'), 'name')
            ->add(Faculty::where('is_active', '=', true), 'title')
            ->add(User::whereHas('userDetail'), 'name')
            ->orderByDesc()
            ->beginWithWildcard()
//            ->orderByRelevance()
            ->includeModelType()
            ->ignoreCase(true)
            ->search("$req");


        $resourceMap = $this->resourceMap;
        $resources = collect($results)->map(function ($result) use ($resourceMap) {
            $resourceClass = $resourceMap[get_class($result)] ?? null;
            return $resourceClass ? new $resourceClass($result) : null;
        });

        $result_type = $this->getCategoriesSearchResult($resources);


        if ($request->query('category')) {
            $resources = $this->sortResourcesByCategory($resources, $request->query('category'));
        }

        $paginate_data = $this->createPaginate($resources, $request, 7);

        $sortedData = $this->sortByType($paginate_data['paginator'], $req);

        return response()->json([
            'searchRes' => $sortedData,
            'result_type' => $result_type,
            'selectedCategory' => ($request->query('category') !== null) ? $request->query('category') : null,
            'paginate' => [
                'current_page' => $paginate_data['paginator']->currentPage(),
                'last_page' => $paginate_data['paginator']->lastPage(),
                'total' => $paginate_data['paginator']->total(),
                'next_page' => $paginate_data['next_page'],
                'prev_page' => $paginate_data['prev_page'],
            ]
        ]);
    }
    private function sortByType(object $data, string $searchRequest): array
    {
        $sortedData = [];

        foreach ($data as $item) {
            $searchData = $item['search_data'] ?? '';
            $matches = $this->getMatches($searchData, $searchRequest);
            $sortedData[$item['type']][] = [
                'data' => $item,
                'matches' => $matches,
                'tag' => $item['type']
            ];
        }

        return $sortedData;
    }

    private function getMatches(string $haystack, string $needle): array
    {
        $matches = [];
//        while (($offset = mb_strpos($haystack, $needle, $offset, 'UTF-8')) !== false) {
//            $left = max(0, $offset - 50);
//            $right = min(mb_strlen($haystack, 'UTF-8'), $offset + 100);
//            $excerpt = mb_substr($haystack, $left, $right - $left, 'UTF-8');
//            $matches[] = $excerpt;
//            $offset += mb_strlen($needle, 'UTF-8');
//        }

        $offset = 0;
        if (($offset = mb_strpos($haystack, $needle, $offset, 'UTF-8')) !== false) {
            for ($i = 0; 1 > count($matches); $i++) {
                $left = max(0, $offset - 50);
                $right = min(mb_strlen($haystack, 'UTF-8'), $offset + 100);
                $excerpt = mb_substr($haystack, $left, $right - $left, 'UTF-8');
                $matches[] = $excerpt;
            }
        }

        return $matches;
    }

    private function sortResourcesByCategory(Collection $resources, string $category) : Collection
    {
        if ($category === "All") {
            $data = $resources;
        } else {
            $data = $resources->where('type', $category);
        }
        return $data;
    }

    private function getCategoriesSearchResult(Collection $resources)
    {
        return $resources->pluck('type')->unique()->values()->all();
    }

    private function createPaginate($resources, $request, $perPage = 10) : array
    {
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        // Отрезаем нужные элементы для текущей страницы
        $currentItems = $resources->slice(($currentPage - 1) * $perPage, $perPage)->all();

        // Создаем экземпляр LengthAwarePaginator
        $paginator = new LengthAwarePaginator($currentItems, count($resources), $perPage, $currentPage, [
            'path' => $request->url(),
            'query' => $request->query,
        ]);

        $nextPage = $paginator->hasMorePages() ? $paginator->currentPage() + 1 : null;
        $prevPage = $paginator->onFirstPage() ? null : $paginator->currentPage() - 1;

        return [
            'paginator' => $paginator,
            'next_page' => $nextPage,
            'prev_page' => $prevPage
        ];
    }
}
