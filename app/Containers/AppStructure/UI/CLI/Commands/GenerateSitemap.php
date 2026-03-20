<?php

namespace App\Containers\AppStructure\UI\CLI\Commands;

use App\Ship\Abstracts\Commands\ConsoleCommand as AbstractConsoleCommand;
use App\Containers\AppStructure\Tasks\GetPagesForSitemapTask;
use App\Containers\AppStructure\Tasks\GetPostsForSitemapTask;
use App\Containers\AppStructure\Tasks\GetDivisionsForSitemapTask;
use App\Containers\AppStructure\Tasks\GetEducationalProgramsForSitemapTask;
use App\Containers\AppStructure\Tasks\GetEventsForSitemapTask;
use App\Containers\AppStructure\Tasks\GetAdditionalEducationsForSitemapTask;
use App\Containers\AppStructure\Tasks\GetFacultiesForSitemapTask;
use App\Containers\AppStructure\Tasks\GetDepartmentsForSitemapTask;
use App\Containers\AppStructure\Tasks\GetUsersForSitemapTask;
use App\Containers\AppStructure\Tasks\AddUrlsToSitemapTask;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends AbstractConsoleCommand
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Генерирует карту сайта';

    public function __construct(
        private readonly GetPagesForSitemapTask $getPagesForSitemapTask,
        private readonly GetPostsForSitemapTask $getPostsForSitemapTask,
        private readonly GetDivisionsForSitemapTask $getDivisionsForSitemapTask,
        private readonly GetEducationalProgramsForSitemapTask $getEducationalProgramsForSitemapTask,
        private readonly GetEventsForSitemapTask $getEventsForSitemapTask,
        private readonly GetAdditionalEducationsForSitemapTask $getAdditionalEducationsForSitemapTask,
        private readonly GetFacultiesForSitemapTask $getFacultiesForSitemapTask,
        private readonly GetDepartmentsForSitemapTask $getDepartmentsForSitemapTask,
        private readonly GetUsersForSitemapTask $getUsersForSitemapTask,
        private readonly AddUrlsToSitemapTask $addUrlsToSitemapTask
    ) {parent::__construct();}

    public function handle()
    {
        $sitemap = Sitemap::create();

        $this->addUrlsToSitemapTask->run($sitemap, $this->getPagesForSitemapTask->run(), function($page) {
            return [
                'route' => 'page.view',
                'params' => ['path' => $page->path],
                'lastModificationDate' => $page->updated_at,
                'priority' => 0.5,
            ];
        });

        $this->addUrlsToSitemapTask->run($sitemap, $this->getPostsForSitemapTask->run(), function($post) {
            return [
                'route' => 'client.post.show',
                'params' => ['slug' => $post->slug],
                'lastModificationDate' => $post->updated_at,
                'priority' => 0.5,
            ];
        });

        $this->addUrlsToSitemapTask->run($sitemap, $this->getDivisionsForSitemapTask->run(), function($division) {
            return [
                'route' => 'client.division.show',
                'params' => ['slug' => $division->slug],
                'lastModificationDate' => $division->updated_at,
                'priority' => 0.5,
            ];
        });

        $this->addUrlsToSitemapTask->run($sitemap, $this->getEducationalProgramsForSitemapTask->run(), function($program) {
            return [
                'route' => 'client.program.show',
                'params' => ['slug' => $program->slug],
                'lastModificationDate' => $program->updated_at,
                'priority' => 0.5,
            ];
        });

        $this->addUrlsToSitemapTask->run($sitemap, $this->getEventsForSitemapTask->run(), function($event) {
            return [
                'route' => 'client.event.show',
                'params' => ['slug' => $event->slug],
                'lastModificationDate' => $event->updated_at,
                'priority' => 0.5,
            ];
        });

        $this->addUrlsToSitemapTask->run($sitemap, $this->getAdditionalEducationsForSitemapTask->run(), function($education) {
            return [
                'route' => 'client.additionalEducation.show',
                'params' => ['slug' => $education->slug],
                'lastModificationDate' => $education->updated_at,
                'priority' => 0.5,
            ];
        });

        $this->addUrlsToSitemapTask->run($sitemap, $this->getFacultiesForSitemapTask->run(), function($faculty) {
            return [
                'route' => 'client.faculty.show',
                'params' => ['slug' => $faculty->slug],
                'lastModificationDate' => $faculty->updated_at,
                'priority' => 0.5,
            ];
        });

        $this->addUrlsToSitemapTask->run($sitemap, $this->getDepartmentsForSitemapTask->run(), function($department) {
            return [
                'route' => 'client.department.show',
                'params' => ['facultySlug' => $department->faculty->slug, 'departmentSlug' => $department->slug],
                'lastModificationDate' => $department->updated_at,
                'priority' => 0.5,
            ];
        });

        $this->addUrlsToSitemapTask->run($sitemap, $this->getUsersForSitemapTask->run(), function($user) {
            return [
                'route' => 'client.person.show',
                'params' => ['slug' => $user->slug],
                'lastModificationDate' => $user->updated_at,
                'priority' => 0.5,
            ];
        });

        $sitemap->writeToFile(public_path('sitemap.xml'));
    }
}
