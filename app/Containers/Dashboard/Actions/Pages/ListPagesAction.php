<?php

namespace App\Containers\Dashboard\Actions\Pages;

use App\Containers\AppStructure\Models\Page;
use App\Containers\AppStructure\Models\SubSection;

class ListPagesAction
{
    public function run(array $filters): array
    {
        $query = Page::query()->with(['section.mainSection']);

        // Search
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('path', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Tab filter
        if (!empty($filters['tab'])) {
            if ($filters['tab'] === 'is_registered') {
                $query->where('is_registered', true)->where('is_url', false);
            } elseif ($filters['tab'] === 'is_url') {
                $query->where('is_url', true);
            } else {
                $query->where('is_registered', false)->where('is_url', false);
            }
        } else {
            // Default: show created pages
            $query->where('is_registered', false)->where('is_url', false);
        }

        // SubSection filter
        if (!empty($filters['sub_section_id'])) {
            $query->where('sub_section_id', $filters['sub_section_id']);
        }

        $pages = $query->orderBy('created_at', 'desc')->paginate(15);

        $subSections = SubSection::with('mainSection')
            ->whereNotNull('title')
            ->orderBy('title')
            ->get();

        return [
            'pages' => $pages,
            'subSections' => $subSections,
        ];
    }
}
