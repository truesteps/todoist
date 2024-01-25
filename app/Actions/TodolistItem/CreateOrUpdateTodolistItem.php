<?php

namespace App\Actions\TodolistItem;

use App\Events\TodolistItem\TodolistItemTransitioned;
use App\Events\TodolistItem\TodolistItemUpdated;
use App\Models\TodolistItem;
use http\Exception\RuntimeException;

class CreateOrUpdateTodolistItem
{
    public function __construct(public TodolistItem $todolistItem, public array $input)
    {
        // do nothing
    }

    public function handle(): TodolistItem
    {
        $todolistId = $this->input['todolist_id'] ?? null;
        $name = $this->input['name'] ?? null;

        if (!$this->todolistItem->exists && !$todolistId) {
            throw new RuntimeException('When creating todolist item, todolist_id must be provided');
        }

        if (!$this->todolistItem->exists && !$name) {
            throw new RuntimeException('Name not provided');
        }

        $this->todolistItem->todolist_id = $todolistId ?? $this->todolistItem->todolist_id;
        $this->todolistItem->name = $name ?? $this->todolistItem->name;

        if (array_key_exists('description', $this->input)) {
            $description = $this->input['description'] ?? null;

            $this->todolistItem->description = is_string($description) && $description !== ''
                ? strip_tags(mb_substr($description, 0, 3000))
                : null;
        }

        // check todolist item as finished
        if (
            array_key_exists('finished', $this->input)
            && $this->input['finished']
            && !$this->todolistItem->finished_at
        ) {
            $this->todolistItem->finished_at = now();
        }

        // uncheck todolist item as finished
        if (
            array_key_exists('finished', $this->input)
            && !$this->input['finished']
            && $this->todolistItem->finished_at
        ) {
            $this->todolistItem->finished_at = null;
        }

        $this->todolistItem->save();

        if ($this->todolistItem->wasRecentlyCreated) {
            event(new TodolistItemTransitioned($this->todolistItem));
        } else {
            event(new TodolistItemUpdated($this->todolistItem));
        }

        return $this->todolistItem;
    }
}
