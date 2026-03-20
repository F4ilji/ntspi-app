<?php

namespace App\Containers\Search\Actions;

use App\Containers\Search\Tasks\BuildStaticFileIndexTask;

class RebuildStaticSearchIndexAction
{
    public function run(): array
    {
        // Clear existing cache (optional, depending on rebuild logic, but typical for full rebuilds)
        // app(ClearStaticSearchCacheAction::class)->run();

        return app(BuildStaticFileIndexTask::class)->run();
    }
}
