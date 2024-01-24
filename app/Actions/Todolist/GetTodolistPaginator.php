<?php

namespace App\Actions\Todolist;

use App\Models\Todolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class GetTodolistPaginator
{
    public function __construct(public array $filters = [])
    {
        // do nothing
    }

    public function handle(): LengthAwarePaginator
    {
        return Todolist::query()
            ->with([
                'todolist_items'
            ])
            ->when($this->filters['search'] ?? null, function (Builder $query, string $search) {
                return $query->where('name', 'like', '%' . $search . '%');
            })
            ->paginate($this->filters['search'] ?? 30);
    }
}
