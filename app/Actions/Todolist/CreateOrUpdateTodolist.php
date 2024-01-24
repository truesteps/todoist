<?php

namespace App\Actions\Todolist;

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
        $description = $this->input['description'] ?? null;

        if (!$name) {
            throw new RuntimeException('Name not provided.');
        }

        $this->todolist->name = $name;
        $this->todolist->description = is_string($description) && $description !== ''
            ? strip_tags(mb_substr($description, 0, 3000))
            : null;
        $this->todolist->save();

        // ToDo: add ability to create todolist and immediately fill it with todolist items

        return $this->todolist;
    }
}
