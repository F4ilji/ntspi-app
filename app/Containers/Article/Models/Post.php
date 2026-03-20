<?php

namespace App\Containers\Article\Models;

use App\Containers\Article\Enums\PostStatus;
use App\Containers\User\Models\User;
use App\Containers\Widget\Models\Slide;
use App\Ship\Traits\HasSeo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\Tags\HasTags;

class Post extends Model
{
    use HasFactory, HasTags, HasSeo;

    protected $guarded = false;

    public function category() : BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function slide(): MorphOne
    {
        return $this->morphOne(Slide::class, 'slidable');
    }

    protected $casts = [
        'content' => 'array',
        'authors' => 'array',
        'status' => PostStatus::class,
        'images' => 'array'
    ];
}
