<?php

namespace App\Containers\AdditionalEducation\Models;

use App\Ship\Enums\Education\FormEducation;
use App\Ship\Models\Model;
use App\Ship\Models\Seo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class AdditionalEducation extends Model
{
    use HasFactory;

    protected $guarded = false;

    protected $casts = [
        'content' => 'array',
        'form_education' => FormEducation::class,
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(AdditionalEducationCategory::class, 'category_id', 'id');
    }

    public function seo(): MorphOne
    {
        return $this->morphOne(Seo::class, 'seoable');
    }
}
