<?php

namespace App\Containers\Article\Tasks;

use App\Containers\Article\Models\Post;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Spatie\Tags\Tag;

class GetTagsTask
{
    public function run()
    {
        return Cache::remember('tags', now()->addHours(1), function () {
            $tagIds = DB::table('taggables')
                ->distinct()
                ->select('tag_id')
                ->where('taggable_type', Post::class)
                ->get()
                ->pluck('tag_id');

            return Tag::whereIn('id', $tagIds)->get();
        });
    }
}