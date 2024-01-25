<?php

namespace Database\Seeders;

use App\Models\Todolist;
use App\Models\TodolistItem;
use Illuminate\Database\Seeder;

class TodolistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Todolist::factory(100)->create();
        TodolistItem::factory(1000)->create();
    }
}
