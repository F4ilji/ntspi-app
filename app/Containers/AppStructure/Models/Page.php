<?php

namespace App\Containers\AppStructure\Models;

use App\Ship\Models\Model;
use App\Ship\Models\Seo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Page extends Model
{
    use HasFactory;

    protected $guarded = false;

    protected static function boot(): void
    {
        parent::boot();

        // Auto-generate path when page is created
        static::creating(function (Page $page) {
            $page->generatePath();
        });

        // Only regenerate path if slug or parent section changed
        static::updating(function (Page $page) {
            if ($page->isDirty(['slug', 'sub_section_id'])) {
                $page->generatePath();
            }
        });
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(SubSection::class, 'sub_section_id');
    }

    public function seo(): MorphOne
    {
        return $this->morphOne(Seo::class, 'seoable');
    }

    protected $casts = [
        'content' => 'array',
        'settings' => 'array',
    ];

    /**
     * Generate path for the page based on slug and parent section
     */
    protected function generatePath(): void
    {
        // Don't regenerate path for registered pages (system routes)
        if ($this->exists && $this->is_registered) {
            return;
        }

        // Only generate path if slug is present
        if (empty($this->slug)) {
            return;
        }

        // Get sub_section_id from model attributes
        $subSectionId = $this->sub_section_id;

        if ($subSectionId === null) {
            $this->path = $this->slug;
            return;
        }

        // Fetch subSection with mainSection relationship
        $subSection = SubSection::with('mainSection')->find($subSectionId);

        if ($subSection === null) {
            $this->path = $this->slug;
            return;
        }

        $mainSection = $subSection->mainSection;

        if ($mainSection === null) {
            $this->path = $subSection->slug . '/' . $this->slug;
            return;
        }

        $this->path = $mainSection->slug . '/' . $subSection->slug . '/' . $this->slug;
    }
}
