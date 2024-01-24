<?php

namespace App\Actions\TodolistItem;

use App\Models\TodolistItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class GetTodolistItemPaginator
{
    public function __construct(public array $filters = [])
    {
        // do nothing
    }

    public function handle(): LengthAwarePaginator
    {
        $search = $this->filters['search'] ?? null;
        $limit = $this->filters['limit'] ?? 30;
        $finished = $this->filters['finished'] ?? false;
        $todolistId = $this->filters['todolist_id'] ?? false;

        return TodolistItem::query()
            ->when($finished, function (Builder $query) {
                return $query->whereNotNull('finished_at');
            })
            ->when($todolistId, function (Builder $query, int $todolistId) {
                return $query->where('todolist_id', '=', $todolistId);
            })
            ->when($search, function (Builder $query, string $search) {
                return $query
                    ->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            })
            ->paginate($limit);
    }
}
