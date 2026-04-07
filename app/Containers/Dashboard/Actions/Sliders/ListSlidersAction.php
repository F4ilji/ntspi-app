<?php

namespace App\Containers\Dashboard\Actions\Sliders;

use App\Containers\Widget\Models\Slider;
use Illuminate\Pagination\LengthAwarePaginator;

class ListSlidersAction
{
    public function run(array $filters = []): LengthAwarePaginator
    {
        $query = Slider::query()
            ->withCount('slides')
            ->when(isset($filters['is_active']), function ($query) use ($filters) {
                $query->where('is_active', $filters['is_active']);
            })
            ->when(isset($filters['search']), function ($query) use ($filters) {
                $query->where(function ($q) use ($filters) {
                    $q->where('title', 'like', '%' . $filters['search'] . '%')
                      ->orWhere('slug', 'like', '%' . $filters['search'] . '%');
                });
            });

        return $query->orderBy('title', 'asc')->paginate(20);
    }
}
