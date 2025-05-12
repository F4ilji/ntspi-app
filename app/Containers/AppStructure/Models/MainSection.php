<?php

namespace App\Containers\AppStructure\Models;

use App\Ship\Models\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MainSection extends Model
{
    use HasFactory;

    protected $guarded = false;

    public function subSections(): HasMany
    {
        return $this->hasMany(SubSection::class);
    }
}
