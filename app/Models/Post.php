<?php

namespace App\Models;

use App\Enums\PostStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;
use Spatie\Tags\HasTags;

class Post extends Model
{
    use HasFactory, HasTags;

    protected $guarded = false;

    protected static function booted()
    {
        static::saved(function ($post) {
            Cache::forget('post_' . $post->id);
            Cache::forget('posts_' . $post->category_id . '_*'); // Очистка кеша для всех постов в категории
        });

        static::deleted(function ($post) {
            Cache::forget('post_' . $post->id);
            Cache::forget('posts_' . $post->category_id . '_*'); // Очистка кеша для всех постов в категории
        });
    }

    public function category() : BelongsTo
    {
        return $this->belongsTo(Category::class);
    }


    public function seo()
    {
        return $this->morphOne(Seo::class, 'seoable');
    }

    protected $casts = [
        'content' => 'array',
        'authors' => 'array',
        'status' => PostStatus::class,
        'images' => 'array'
    ];
}
