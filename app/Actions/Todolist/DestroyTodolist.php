<?php

namespace App\Actions\Todolist;

use App\Events\Todolist\TodolistTransitioned;
use App\Models\Todolist;
use http\Exception\RuntimeException;
use Throwable;

class DestroyTodolist
{
    public function __construct(public Todolist $todolist)
    {
        // do nothing
    }

    /**
     * Soft-delete todolist
     *
     * @return void
     */
    public function handle(): void
    {
        if ($this->todolist->trashed()) {
            throw new RuntimeException('Todolist already deleted');
        }

        try {
            $this->todolist->delete();
        } catch (Throwable $throwable) {
            report($throwable);

            throw new RuntimeException('Something went wrong while trying to delete todolist, try again later');
        }

        event(new TodolistTransitioned($this->todolist));
    }
}
