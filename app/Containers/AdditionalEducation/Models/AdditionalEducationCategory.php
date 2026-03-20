<?php

namespace App\Containers\AdditionalEducation\Models;

use App\Ship\Models\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdditionalEducationCategory extends Model
{
    use HasFactory;

    protected $guarded = false;

    public function scopeWithActivePrograms(Builder $query): Builder
    {
        return $query->with(['additionalEducations' => function ($query) {
            $query->where('is_active', true);
        }]);
    }

    public function additionalEducations(): HasMany
    {
        return $this->hasMany(AdditionalEducation::class, 'category_id', 'id');
    }

    public function direction(): BelongsTo
    {
        return $this->belongsTo(DirectionAdditionalEducation::class, 'dir_addit_educat_id', 'id');
    }
}
