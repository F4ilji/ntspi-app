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

    public function section() : BelongsTo
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
}
