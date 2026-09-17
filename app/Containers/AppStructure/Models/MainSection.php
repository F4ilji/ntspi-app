<?php

namespace App\Containers\AppStructure\Models;

use App\Ship\Models\Model;
use App\Ship\Traits\HasContainerFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MainSection extends Model
{
    use HasContainerFactory;

    protected static string $factory = \Database\Factories\MainSectionFactory::class;

    protected $guarded = false;

    public function subSections(): HasMany
    {
        return $this->hasMany(SubSection::class);
    }
}
