<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdditionalEducationSearchResource;
use App\Http\Resources\EducationalProgramSearchResource;
use App\Http\Resources\EducationGroupSearchResource;
use App\Http\Resources\EventSearchResource;
use App\Http\Resources\PageResource;
use App\Http\Resources\PageSearchResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\PostSearchResource;
use App\Models\AdditionalEducation;
use App\Models\EducationalGroup;
use App\Models\EducationalProgram;
use App\Models\Event;
use App\Models\Page;
use App\Models\Post;
use Filament\Notifications\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use ProtoneMedia\LaravelCrossEloquentSearch\Search;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $req = Str::lower($request->query('search'));
        if (!$req) {
            return response()->json([
                'searchRes' => null,
            ]);
        }
        $results = Search::new()
            ->add(Post::where('status', '=', 'published'), ['title', 'search_data'])
            ->add(Page::where('searchable', '=', true), ['title', 'search_data'])
            ->add(Event::where('event_date_start', '>', Date::now()), 'title')
            ->add(AdditionalEducation::where('is_active', '=', true), 'title')
            ->add(EducationalGroup::with('schedules'), 'title')
            ->add(EducationalProgram::where('status', '=', true)->whereHas('admission_plans'), 'name')
            ->beginWithWildcard()
            ->orderByRelevance()
            ->includeModelType()
            ->ignoreCase(true)
            ->search($req);

        $resources = collect($results)->map(function ($result) {
            if ($result instanceof Post) {
                return new PostSearchResource($result);
            }
            if ($result instanceof Page) {
                return new PageSearchResource($result);
            }
            if ($result instanceof EducationalGroup) {
                return new EducationGroupSearchResource($result);
            }
            if ($result instanceof EducationalProgram) {
                return new EducationalProgramSearchResource($result);
            }
            if ($result instanceof Event) {
                return new EventSearchResource($result);
            }
            if ($result instanceof AdditionalEducation) {
                return new AdditionalEducationSearchResource($result);
            }
        });

        $limitedRes = $resources->take(10);

        return response()->json([
            'searchRes' => $this->sortByType($limitedRes, $req),
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
        $offset = 0;
        while (($offset = mb_strpos($haystack, $needle, $offset, 'UTF-8')) !== false) {
            $left = max(0, $offset - 50);
            $right = min(mb_strlen($haystack, 'UTF-8'), $offset + 100);
            $excerpt = mb_substr($haystack, $left, $right - $left, 'UTF-8');
            $matches[] = $excerpt;
            $offset += mb_strlen($needle, 'UTF-8');
        }
        return $matches;
    }


}
