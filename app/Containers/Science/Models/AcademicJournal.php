<?php

namespace App\Containers\Science\Models;

use App\Ship\Contracts\SeoDescriptionInterface;
use App\Ship\Models\Model;
use App\Ship\Traits\HasSeo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicJournal extends Model implements SeoDescriptionInterface
{
    use HasFactory, HasSeo;

    protected $guarded = false;

    protected $casts = [
        'main_info' => 'array',
        'chief_editor' => 'array',
        'editors' => 'array',
        'for_authors' => 'array',
    ];

    public function journals(): HasMany
    {
        return $this->hasMany(JournalIssue::class);
    }

    public function getSeoDescription(): array
    {
        return $this->main_info;
    }
}
