<?php

namespace App\Actions\Todolist;

use App\Events\Todolist\TodolistTransitioned;
use App\Events\Todolist\TodolistUpdated;
use App\Models\Todolist;
use http\Exception\RuntimeException;

class CreateOrUpdateTodolist
{
    public function __construct(public Todolist $todolist, public array $input)
    {
        // do nothing
    }

    public function handle(): Todolist
    {
        $name = $this->input['name'] ?? null;

        if (!$name) {
            throw new RuntimeException('Name not provided');
        }

        $this->todolist->name = strip_tags($name);

        if (array_key_exists('description', $this->input)) {
            $description = $this->input['description'] ?? null;

            $this->todolist->description = is_string($description) && $description !== ''
                ? strip_tags(mb_substr($description, 0, 3000))
                : null;
        }

        $this->todolist->save();

        if ($this->todolist->wasRecentlyCreated) {
            event(new TodolistTransitioned($this->todolist));
        } else {
            event(new TodolistUpdated($this->todolist));
        }

        return $this->todolist;
    }
}
