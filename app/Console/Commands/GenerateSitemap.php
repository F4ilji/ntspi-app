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

        // Генерация карты сайта для всех моделей
        $this->generatePages($sitemap);
        $this->generatePosts($sitemap);
        $this->generateDivisions($sitemap);
        $this->generateEducationPrograms($sitemap); // Добавляем генерацию для EducationProgram
        $this->generateEvents($sitemap); // Добавляем генерацию для Event
        $this->generateAdditionalEducations($sitemap); // Добавляем генерацию для AdditionalEducation
        $this->generateFaculties($sitemap); // Добавляем генерацию для Faculty
        $this->generateDepartments($sitemap); // Добавляем генерацию для Department
        $this->generateUsers($sitemap); // Добавляем генерацию для User

        // Сохраняем карту сайта в файл
        $sitemap->writeToFile(public_path('sitemap.xml'));
    }

    protected function generatePages(Sitemap $sitemap)
    {
        $pages = Page::query()
            ->where('is_visible', true)
            ->where('code', 200)
            ->where('path', '!=', null)
            ->where('is_url', false)
            ->get();

        $this->addUrlsToSitemap($sitemap, $pages, function($page) {
            return [
                'route' => 'page.view',
                'params' => ['path' => $page->path],
                'lastModificationDate' => $page->updated_at,
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
                'route' => 'client.post.show',
                'params' => ['slug' => $post->slug],
                'lastModificationDate' => $post->updated_at,
                'priority' => 0.5,
            ];
        });
    }

    protected function generateDivisions(Sitemap $sitemap)
    {
        $divisions = Division::query()
            ->where('is_active', true)
            ->get();

        $this->addUrlsToSitemap($sitemap, $divisions, function($division) {
            return [
                'route' => 'client.division.show',
                'params' => ['slug' => $division->slug],
                'lastModificationDate' => $division->updated_at,
                'priority' => 0.5,
            ];
        });
    }

    protected function generateEducationPrograms(Sitemap $sitemap)
    {
        $programs = EducationalProgram::query()
            ->where('status', EducationalProgramStatus::PUBLISHED) // Пример условия для активных программ
            ->get();

        $this->addUrlsToSitemap($sitemap, $programs, function($program) {
            return [
                'route' => 'client.program.show',
                'params' => ['slug' => $program->slug],
                'lastModificationDate' => $program->updated_at,
                'priority' => 0.5,
            ];
        });
    }

    protected function generateEvents(Sitemap $sitemap)
    {
        $now = now()->toDateString(); // Текущая дата

        $events = Event::query()
            ->where('event_date_end', '>=', $now) // Только актуальные события
            ->get();

        $this->addUrlsToSitemap($sitemap, $events, function($event) {
            return [
                'route' => 'client.event.show',
                'params' => ['slug' => $event->slug],
                'lastModificationDate' => $event->updated_at,
                'priority' => 0.5,
            ];
        });
    }

    protected function generateAdditionalEducations(Sitemap $sitemap)
    {
        $educations = AdditionalEducation::query()
            ->where('is_active', true) // Пример условия для активных программ
            ->get();

        $this->addUrlsToSitemap($sitemap, $educations, function($education) {
            return [
                'route' => 'client.additionalEducation.show',
                'params' => ['slug' => $education->slug],
                'lastModificationDate' => $education->updated_at,
                'priority' => 0.5,
            ];
        });
    }

    protected function generateFaculties(Sitemap $sitemap)
    {
        $faculties = Faculty::query()
            ->where('is_active', true) // Пример условия для активных факультетов
            ->get();

        $this->addUrlsToSitemap($sitemap, $faculties, function($faculty) {
            return [
                'route' => 'client.faculty.show',
                'params' => ['slug' => $faculty->slug],
                'lastModificationDate' => $faculty->updated_at,
                'priority' => 0.5,
            ];
        });
    }

    protected function generateDepartments(Sitemap $sitemap)
    {
        $departments = Department::query()
            ->where('is_active', true) // Пример условия для активных кафедр
            ->get();

        $this->addUrlsToSitemap($sitemap, $departments, function($department) {
            return [
                'route' => 'client.department.show',
                'params' => ['facultySlug' => $department->faculty->slug, 'departmentSlug' => $department->slug],
                'lastModificationDate' => $department->updated_at,
                'priority' => 0.5,
            ];
        });
    }

    protected function generateUsers(Sitemap $sitemap)
    {
        $users = User::query()
            ->whereHas('userDetail', function ($q) {
                $q->where('is_only_worker', false);
            })
            ->get();

        $this->addUrlsToSitemap($sitemap, $users, function($user) {
            return [
                'route' => 'client.person.show',
                'params' => ['slug' => $user->slug],
                'lastModificationDate' => $user->updated_at,
                'priority' => 0.5,
            ];
        });
    }

    protected function addUrlsToSitemap(Sitemap $sitemap, $items, callable $callback)
    {
        foreach ($items as $item) {
            $urlData = $callback($item);
            $url = route($urlData['route'], $urlData['params']);
            $sitemap->add(Url::create($url)
                ->setLastModificationDate($urlData['lastModificationDate'])
                ->setPriority($urlData['priority']));
        }
    }
}
