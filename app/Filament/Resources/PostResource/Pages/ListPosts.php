<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Containers\Article\Enums\PostStatus;
use App\Containers\Article\Models\Post;
use App\Filament\Resources\PostResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    public function getPostsByStatuses(): \Illuminate\Support\Collection
    {
        return Post::select('status', DB::raw('count(*) as post_count'))
            ->groupBy('status')
            ->when($this->canViewOnlyOwnRecords(), function (Builder $query) {
                $query->where('user_id', auth()->id());
            })
            ->pluck('post_count', 'status');
    }

    protected function canViewOnlyOwnRecords(): bool
    {
        return auth()->user()->can('view_only_own_records_post');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $postsByStatuses = $this->getPostsByStatuses();

        return [
            'status' => Tab::make('Новости на рассмотрении')->modifyQueryUsing(function (Builder $query) {
                $query->where('status', '=', PostStatus::VERIFICATION->value);
                if ($this->canViewOnlyOwnRecords()) {
                    $query->where('user_id', auth()->id());
                }
            })->badge($postsByStatuses[PostStatus::VERIFICATION->value] ?? 0),

            'All' => Tab::make('Все новости')->modifyQueryUsing(function (Builder $query) {
                if ($this->canViewOnlyOwnRecords()) {
                    $query->where('user_id', auth()->id());
                }
            }),
        ];
    }
}
