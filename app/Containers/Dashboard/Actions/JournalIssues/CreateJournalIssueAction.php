<?php

namespace App\Containers\Dashboard\Actions\JournalIssues;

use App\Containers\Science\Models\JournalIssue;

class CreateJournalIssueAction
{
    public function run(array $data): JournalIssue
    {
        return JournalIssue::create($data);
    }
}
