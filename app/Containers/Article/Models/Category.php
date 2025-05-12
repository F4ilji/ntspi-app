<?php

namespace App\Containers\Article\Models;

use App\Ship\Models\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $guarded = false;

    public function posts() : HasMany
    {
        return $this->hasMany(Post::class);
    }
}
