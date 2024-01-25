<?php

namespace App\Actions\Todolist;

use App\Models\Todolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Pagination\LengthAwarePaginator;

class GetTodolistPaginator
{
    public function __construct(public array $filters = [])
    {
        // do nothing
    }

    public function handle(): LengthAwarePaginator
    {
        $search = $this->filters['search'] ?? null;
        $limit = $this->filters['limit'] ?? 30;
        $archived = $this->filters['archived'] ?? false;

        return Todolist::query()
            ->when($archived, function (Builder $query) {
                /** @var Builder|SoftDeletes $query */
                return $query->onlyTrashed();
            })
            ->when($search, function (Builder $query, string $search) {
                return $query
                    ->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            })
            ->withCount('todolist_items')
            ->paginate($limit);
    }
}
