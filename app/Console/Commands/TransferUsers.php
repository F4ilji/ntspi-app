<?php

namespace App\Console\Commands;

use App\Jobs\ImportUsers;
use Illuminate\Console\Command;

class TransferUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:transfer-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        ImportUsers::dispatch();
    }
}
