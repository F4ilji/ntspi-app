<?php

namespace App\Containers\Widget\Models;

use App\Ship\Traits\HasContainerFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Slider extends Model
{
    use HasContainerFactory;

    protected static string $factory = \Database\Factories\SliderFactory::class;

    protected $guarded = false;


    public function slides(): HasMany
    {
        return $this->hasMany(Slide::class);
    }

}
