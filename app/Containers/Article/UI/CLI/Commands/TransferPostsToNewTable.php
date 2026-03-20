<?php

namespace App\Containers\Article\UI\CLI\Commands;

use App\Jobs\ImportApiDataPost;
use App\Ship\Abstracts\Commands\ConsoleCommand as AbstractConsoleCommand;

class TransferPostsToNewTable extends AbstractConsoleCommand
{

    protected $signature = 'import:api';
    protected $description = 'Импортировать данные из статического SQL файла в базу данных';


    public function handle()
    {
        ImportApiDataPost::dispatch();
    }
}
