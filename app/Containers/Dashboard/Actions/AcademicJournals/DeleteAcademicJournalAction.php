<?php

namespace App\Containers\Dashboard\Actions\AcademicJournals;

use App\Containers\Science\Models\AcademicJournal;

class DeleteAcademicJournalAction
{
    public function run(AcademicJournal $journal): bool
    {
        return $journal->delete();
    }
}
