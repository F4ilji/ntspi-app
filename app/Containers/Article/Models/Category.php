<?php

namespace App\Containers\Article\Models;

use App\Ship\Models\Model;
use App\Ship\Traits\HasContainerFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasContainerFactory;

    protected static string $factory = \Database\Factories\CategoryFactory::class;

    protected $guarded = false;

    public function posts() : HasMany
    {
        return $this->hasMany(Post::class);
    }
}
