<?php

namespace App\Console\Commands;

use App\Enums\EducationalProgramStatus;
use App\Enums\PostStatus;
use App\Models\AcademicJournal;
use App\Models\AdditionalEducation;
use App\Models\Department;
use App\Models\Division;
use App\Models\EducationalProgram;
use App\Models\Event;
use App\Models\Faculty;
use App\Models\LibraryNews;
use App\Models\Page;
use App\Models\Post;
use App\Models\User;
use App\Models\VirtualExhibition;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Генерирует карту сайта';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sitemap = Sitemap::create();

        $this->generatePages($sitemap);
        $this->generatePosts($sitemap);
        $this->generateEvents($sitemap);
        $this->generateFaculties($sitemap);
        $this->generateDepartments($sitemap);
        $this->generateDivisisons($sitemap);
        $this->generateEducationalPrograms($sitemap);
        $this->generateAdditionalPrograms($sitemap);
        $this->generateAcademicJournals($sitemap);
        $this->generateVirtualExhibitions($sitemap);
        $this->generateLibraryNews($sitemap);

        $sitemap->writeToFile(public_path('sitemap.xml'));
    }

    protected function generatePages(Sitemap $sitemap)
    {
        $pages = Page::query()
            ->where('is_visible', true)
            ->where('code', 200)
            ->get();

        $this->addUrlsToSitemap($sitemap, $pages, function($page) {
            return [
                'path' => "/{$page->path}",
                'lastModificationDate' => $page->updated_at,
                'priority' => 0.5,
            ];
        });
    }

    protected function generateEvents(Sitemap $sitemap)
    {
        $events = Event::query()
            ->get();

        $this->addUrlsToSitemap($sitemap, $events, function($event) {
            return [
                'path' => route('client.event.show', $event->slug, false),
                'lastModificationDate' => $event->updated_at,
                'priority' => 0.5,
            ];
        });
    }

    protected function generatePosts(Sitemap $sitemap)
    {
        $posts = Post::query()
            ->where('status', PostStatus::PUBLISHED)
            ->get();

        $this->addUrlsToSitemap($sitemap, $posts, function($post) {
            return [
                'path' => route('client.post.show', $post->slug, false),
                'lastModificationDate' => $post->updated_at,
                'priority' => 0.5,
            ];
        });
    }

    protected function generateEducationalPrograms(Sitemap $sitemap)
    {
        $posts = EducationalProgram::query()
            ->where('status', EducationalProgramStatus::PUBLISHED)
            ->whereHas('admission_plans')
            ->get();

        $this->addUrlsToSitemap($sitemap, $posts, function($program) {
            return [
                'path' => route('client.program.show', $program->slug, false),
                'lastModificationDate' => $program->updated_at,
                'priority' => 0.5,
            ];
        });
    }

    protected function generateAdditionalPrograms(Sitemap $sitemap)
    {
        $posts = AdditionalEducation::query()
            ->where('is_active', true)
            ->get();

        $this->addUrlsToSitemap($sitemap, $posts, function($program) {
            return [
                'path' => route('client.additionalProgram.show', $program->slug, false),
                'lastModificationDate' => $program->updated_at,
                'priority' => 0.5,
            ];
        });
    }

    protected function generateFaculties(Sitemap $sitemap)
    {
        $posts = Faculty::query()
            ->where('is_active', true)
            ->get();

        $this->addUrlsToSitemap($sitemap, $posts, function($faculty) {
            return [
                'path' => route('client.faculty.show', $faculty->slug, false),
                'lastModificationDate' => $faculty->updated_at,
                'priority' => 0.5,
            ];
        });
    }

    protected function generateDepartments(Sitemap $sitemap)
    {
        $posts = Department::query()
            ->with('faculty')
            ->where('is_active', true)
            ->get();

        $this->addUrlsToSitemap($sitemap, $posts, function($department) {
            return [
                'path' => route('client.department.show', [
                    'facultySlug' => $department->faculty->slug,
                    'departmentSlug' => $department->slug,
                ], false),
                'lastModificationDate' => $department->updated_at,
                'priority' => 0.5,
            ];
        });
    }

    protected function generateDivisisons(Sitemap $sitemap)
    {
        $posts = Division::query()
            ->where('is_active', true)
            ->get();

        $this->addUrlsToSitemap($sitemap, $posts, function($division) {
            return [
                'path' => route('client.division.show', $division->slug, false),
                'lastModificationDate' => $division->updated_at,
                'priority' => 0.5,
            ];
        });
    }

    protected function generateAcademicJournals(Sitemap $sitemap)
    {
        $posts = AcademicJournal::query()
            ->where('is_active', true)
            ->get();

        $this->addUrlsToSitemap($sitemap, $posts, function($journal) {
            return [
                'path' => route('client.academicJournal.show', $journal->slug, false),
                'lastModificationDate' => $journal->updated_at,
                'priority' => 0.5,
            ];
        });
    }

    protected function generateLibraryNews(Sitemap $sitemap)
    {
        $posts = LibraryNews::query()
            ->where('is_active', true)
            ->get();

        $this->addUrlsToSitemap($sitemap, $posts, function($journal) {
            return [
                'path' => route('client.library.news.show', $journal->slug, false),
                'lastModificationDate' => $journal->updated_at,
                'priority' => 0.5,
            ];
        });
    }

    protected function generateVirtualExhibitions(Sitemap $sitemap)
    {
        $posts = VirtualExhibition::query()
            ->where('is_active', true)
            ->get();

        $this->addUrlsToSitemap($sitemap, $posts, function($exhibition) {
            return [
                'path' => route('client.library.exhibition.show', $exhibition->slug, false),
                'lastModificationDate' => $exhibition->updated_at,
                'priority' => 0.5,
            ];
        });
    }

    protected function generatePersons(Sitemap $sitemap)
    {
        $posts = User::query()
            ->whereHas('userDetail')
            ->with('userDetail')
            ->get();

        $this->addUrlsToSitemap($sitemap, $posts, function($user) {
            return [
                'path' => route('client.person.show', $user->slug, false),
                'lastModificationDate' => $user->userDetail->updated_at,
                'priority' => 0.5,
            ];
        });
    }




    protected function addUrlsToSitemap(Sitemap $sitemap, $items, callable $callback)
    {
        foreach ($items as $item) {
            $urlData = $callback($item);
            $sitemap->add(Url::create($urlData['path'])
                ->setLastModificationDate($urlData['lastModificationDate'])
                ->setPriority($urlData['priority']));
        }
    }
}
