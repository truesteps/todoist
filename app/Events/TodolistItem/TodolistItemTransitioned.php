<?php

namespace App\Events\TodolistItem;

use App\Events\PublicBroadcast;
use App\Models\TodolistItem;

class TodolistItemTransitioned extends PublicBroadcast
{
    public array $todolistItem;

    /**
     * Create a new event instance.
     *
     * @param TodolistItem $todolistItem
     */
    public function __construct(TodolistItem $todolistItem)
    {
        $this->todolistItem = [
            'id' => $todolistItem->id,
            'todolist_id' => $todolistItem->todolist_id,
        ];
    }
}
