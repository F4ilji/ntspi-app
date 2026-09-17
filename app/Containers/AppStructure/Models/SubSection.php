<?php

namespace App\Containers\AppStructure\Models;

use App\Ship\Models\Model;
use App\Ship\Traits\HasContainerFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubSection extends Model
{
    use HasContainerFactory;

    protected static string $factory = \Database\Factories\SubSectionFactory::class;

    protected $guarded = false;

    public function mainSection() : BelongsTo
    {
        return $this->belongsTo(MainSection::class);
    }

    public function pages() : HasMany
    {
        return $this->hasMany(Page::class)->orderBy('sort', 'asc');
    }
}
