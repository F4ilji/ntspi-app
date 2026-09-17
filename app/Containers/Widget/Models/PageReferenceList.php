<?php

namespace App\Containers\Widget\Models;

use App\Ship\Traits\HasContainerFactory;
use Illuminate\Database\Eloquent\Model;

class PageReferenceList extends Model
{
    use HasContainerFactory;

    protected static string $factory = \Database\Factories\PageReferenceListFactory::class;

    protected $guarded = false;

    protected $casts = [
        'content' => 'array',
    ];
}
