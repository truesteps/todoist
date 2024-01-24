<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * Columns
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 *
 * Relationships
 *
 * @property-read Collection|TodolistItem[] $todolist_items
 */
class Todolist extends Model
{
    use HasFactory;
    use SoftDeletes;

    // region Relationships

    public function todolist_items(): HasMany
    {
        return $this->hasMany(TodolistItem::class);
    }

    // endregion
}
