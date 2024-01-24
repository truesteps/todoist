<?php

namespace App\Actions\TodolistItem;

use App\Events\TodolistItem\TodolistItemTransitioned;
use App\Models\TodolistItem;
use http\Exception\RuntimeException;
use Throwable;

class DestroyTodolistItem
{
    public function __construct(public TodolistItem $todolistItem)
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
        try {
            $this->todolistItem->delete();
        } catch (Throwable $throwable) {
            report($throwable);

            throw new RuntimeException('Something went wrong while trying to delete todolist item, try again later');
        }

        event(new TodolistItemTransitioned($this->todolistItem));
    }
}
