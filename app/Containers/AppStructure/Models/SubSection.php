<?php

namespace App\Containers\AppStructure\Models;

use App\Ship\Models\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubSection extends Model
{
    use HasFactory;

    protected $guarded = false;

    public function mainSection() : BelongsTo
    {
        return $this->belongsTo(MainSection::class);
    }

    public function pages() : HasMany
    {
        return $this->hasMany(Page::class);
    }
}
