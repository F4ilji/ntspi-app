<?php

namespace App\Containers\AppStructure\UI\CLI\Commands;

use App\Ship\Abstracts\Commands\ConsoleCommand as AbstractConsoleCommand;
use App\Containers\AppStructure\Tasks\RegisterApplicationRoutesTask;

class RegisterRoutes extends AbstractConsoleCommand
{
    protected $signature = 'routes:register';
    protected $description = 'Register application routes in the database';

    public function __construct(private readonly RegisterApplicationRoutesTask $registerApplicationRoutesTask)
    {parent::__construct();}

    public function handle()
    {
        $this->registerApplicationRoutesTask->run();
        $this->info('Все маршруты успешно проверены.');
    }
}
