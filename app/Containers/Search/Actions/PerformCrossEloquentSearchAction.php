<?php

namespace App\Containers\Search\Actions;

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
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use ProtoneMedia\LaravelCrossEloquentSearch\Search;
use Illuminate\Support\Collection;

class PerformCrossEloquentSearchAction
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

    public function run(string $query): Collection
    {
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
            ->includeModelType()
            ->ignoreCase(true)
            ->search("$query");

        $resourceMap = $this->resourceMap;
        $resources = collect($results)->map(function ($result) use ($resourceMap) {
            $resourceClass = $resourceMap[get_class($result)] ?? null;
            return $resourceClass ? new $resourceClass($result) : null;
        })->filter(); // Filter out nulls from resources

        return $resources;
    }
}
