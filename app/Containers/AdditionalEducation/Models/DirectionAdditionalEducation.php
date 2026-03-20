<?php

namespace App\Containers\AdditionalEducation\Models;

use App\Ship\Models\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DirectionAdditionalEducation extends Model
{
    use HasFactory;

    protected $guarded = false;


    public function additionalEducationCategories(): HasMany
    {
        return $this->hasMany(AdditionalEducationCategory::class, 'dir_addit_educat_id', 'id');
    }
}
