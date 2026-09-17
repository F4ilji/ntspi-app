<?php

namespace App\Containers\AdditionalEducation\Models;

use App\Ship\Models\Model;
use App\Ship\Traits\HasContainerFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DirectionAdditionalEducation extends Model
{
    use HasContainerFactory;

    protected static string $factory = \Database\Factories\DirectionAdditionalEducationFactory::class;

    protected $guarded = false;


    public function additionalEducationCategories(): HasMany
    {
        return $this->hasMany(AdditionalEducationCategory::class, 'dir_addit_educat_id', 'id');
    }
}
