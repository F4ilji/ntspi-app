<?php

namespace App\Containers\Dashboard\Actions\JournalIssues;

use App\Containers\Science\Models\JournalIssue;

class UpdateJournalIssueAction
{
    public function run(JournalIssue $issue, array $data): JournalIssue
    {
        $issue->update($data);
        return $issue->fresh();
    }
}
