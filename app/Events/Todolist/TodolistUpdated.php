<?php

namespace App\Events\Todolist;

use App\Events\PublicBroadcast;
use App\Models\Todolist;

class TodolistUpdated extends PublicBroadcast
{
    public array $todolist;

    /**
     * Create a new event instance.
     *
     * @param Todolist $todolist
     */
    public function __construct(Todolist $todolist)
    {
        $this->todolist = [
            'id' => $todolist->id,
        ];
    }
}
