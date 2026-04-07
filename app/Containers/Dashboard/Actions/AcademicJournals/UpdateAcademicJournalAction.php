<?php

namespace App\Containers\Dashboard\Actions\AcademicJournals;

use App\Containers\Science\Models\AcademicJournal;

class UpdateAcademicJournalAction
{
    public function run(AcademicJournal $journal, array $data): AcademicJournal
    {
        $journal->update($data);
        return $journal->fresh();
    }
}
