<?php

namespace App\Console\Commands;

use App\Jobs\ImportApiDataPost;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class TransferPostsToNewTable extends Command
{
    protected $signature = 'import:api';
    protected $description = 'Импортировать данные из статического SQL файла в базу данных';

    public function handle()
    {
        ImportApiDataPost::dispatch();
    }
}