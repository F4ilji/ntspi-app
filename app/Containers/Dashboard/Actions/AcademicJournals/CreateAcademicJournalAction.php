<?php

namespace App\Containers\Dashboard\Actions\AcademicJournals;

use App\Containers\Science\Models\AcademicJournal;

class CreateAcademicJournalAction
{
    public function run(array $data): AcademicJournal
    {
        return AcademicJournal::create($data);
    }
}
