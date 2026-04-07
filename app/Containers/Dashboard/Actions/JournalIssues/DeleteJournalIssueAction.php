<?php

namespace App\Containers\Dashboard\Actions\JournalIssues;

use App\Containers\Science\Models\JournalIssue;

class DeleteJournalIssueAction
{
    public function run(JournalIssue $issue): bool
    {
        return $issue->delete();
    }
}
