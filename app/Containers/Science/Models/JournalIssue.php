<?php

namespace App\Containers\Science\Models;

use App\Ship\Models\Model;
use App\Ship\Traits\HasContainerFactory;

class JournalIssue extends Model
{
    use HasContainerFactory;

    protected static string $factory = \Database\Factories\JournalIssueFactory::class;

    protected $guarded = false;
}
