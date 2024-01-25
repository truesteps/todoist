<?php

namespace Database\Factories;

use App\Models\Todolist;
use Illuminate\Database\Eloquent\Factories\Factory;
use Random\RandomException;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TodolistItem>
 */
class TodolistItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * @throws RandomException
     */
    public function definition(): array
    {
        return [
            'todolist_id' => Todolist::query()->inRandomOrder()->first()?->id,
            'name' => fake()->realText(150),
            'description' => random_int(0, 100) > 50 ? fake()->realText(300) : null,
            'finished_at' => random_int(0, 100) > 50 ? now() : null,
        ];
    }
}
