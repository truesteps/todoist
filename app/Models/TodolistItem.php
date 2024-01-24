<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Columns
 *
 * @property int $id
 * @property int $todolist_id
 * @property string $name
 * @property string|null $description
 * @property Carbon|null $finished_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relationships
 *
 * @property-read Todolist $todolist
 *
 * Attributes
 *
 * @property-read bool $is_finished
 */
class TodolistItem extends Model
{
    use HasFactory;

    // region Relationships

    public function todolist(): BelongsTo
    {
        return $this->belongsTo(Todolist::class)->withTrashed();
    }

    // endregion

    // region Attributes

    public function isFinished(): Attribute
    {
        return new Attribute(get: fn() => $this->finished_at !== null);
    }

    // endregion
}
