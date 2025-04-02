<?php

namespace App\Models;

use App\Services\App\Seo\SeoDescriptionInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicJournal extends Model implements SeoDescriptionInterface
{
    use HasFactory;

    protected $guarded = false;

    protected $casts = [
        'main_info' => 'array',
        'chief_editor' => 'array',
        'editors' => 'array',
        'for_authors' => 'array',
    ];

    public function journals()
    {
        return $this->hasMany(JournalIssue::class);
    }

    public function seo()
    {
        return $this->morphOne(Seo::class, 'seoable');
    }

    public function getSeoDescription(): array
    {
        return $this->main_info;
    }
}
