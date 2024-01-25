<?php

namespace Tests\Feature;

use App\Events\TodolistItem\TodolistItemTransitioned;
use App\Events\TodolistItem\TodolistItemUpdated;
use App\Models\Todolist;
use App\Models\TodolistItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Random\RandomException;
use Tests\TestCase;

class TodolistItemTest extends TestCase
{
    use RefreshDatabase;

    protected $defaultHeaders = [
        'Accept' => 'application/json'
    ];

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    /**
     * @return void
     */
    public function test_the_application_returns_a_list_of_todolist_items_when_index_called(): void
    {
        $response = $this->get('/api/todolist-item');

        // response is successful
        $response->assertStatus(200);
        // response returned paginator object
        $response->assertJson([
            'paginator' => [
                'data' => [],
            ]
        ]);
        // response returned some data
        $this->assertNotEmpty($response->json('paginator.data'));
    }

    /**
     * @return void
     */
    public function test_the_application_returns_a_list_of_todolist_items_for_specific_todolist_when_index_called(
    ): void
    {
        /** @var Todolist|null $todolist */
        $todolist = Todolist::query()->inRandomOrder()->first();

        if (!$todolist) {
            throw new \RuntimeException('Todolists not found, seeder probably didn\'t run');
        }

        $response = $this->get('/api/todolist-item?todolist_id=' . $todolist->id);

        // response is successful
        $response->assertStatus(200);
        // response returned paginator object
        $response->assertJson([
            'paginator' => [
                'data' => [],
            ]
        ]);
        // response returned some data
        $this->assertNotEmpty($response->json('paginator.data'));

        // response only contains todolist items that belong to originally queried todolist
        foreach ($response->json('paginator.data.*.todolist_id') as $todolistId) {
            $this->assertEquals($todolist->id, $todolistId);
        }
    }

    /**
     * @return void
     */
    public function test_the_application_returns_todolist_item_with_todolist_when_show_called(): void
    {
        /** @var TodolistItem|null $todolistItem */
        $todolistItem = TodolistItem::query()->inRandomOrder()->first();

        if (!$todolistItem) {
            throw new \RuntimeException('TodolistsItem not found, seeder probably didn\'t run');
        }

        $response = $this->get('/api/todolist-item/' . $todolistItem->id);

        // response is successful
        $response->assertStatus(200);
        // response returned paginator object
        $response->assertJson([
            'todolist' => [],
            'todolistItem' => []
        ]);
        // response returned some data
        $this->assertNotEmpty($response->json('todolist'));
        $this->assertNotEmpty($response->json('todolistItem'));
    }

    /**
     * @retur void
     * @throws RandomException
     */
    public function test_the_application_can_create_todolist_item_when_store_called(): void
    {
        Event::fake();

        /** @var Todolist|null $todolist */
        $todolist = Todolist::query()->inRandomOrder()->first();

        if (!$todolist) {
            throw new \RuntimeException('Todolists not found, seeder probably didn\'t run');
        }

        $name = fake()->realText(150);
        $description = random_int(0, 100) > 50 ? fake()->realTextBetween(2, 300) : null;

        $response = $this->post('/api/todolist-item', [
            'todolist_id' => $todolist->id,
            'name' => $name,
            'description' => $description,
        ]);

        // response is successful
        $response->assertStatus(201);
        // response returned todolist object
        $response->assertJson([
            'todolistItem' => [],
        ]);
        // response returned todolistItem with name provided
        $this->assertEquals($name, $response->json('todolistItem.name'));
        // response returned todolistItem with description provided
        $this->assertEquals($description, $response->json('todolistItem.description'));
        // TodolistItemTransitioned event dispatched
        Event::assertDispatched(TodolistItemTransitioned::class);
    }

    /**
     * @return void
     * @throws RandomException
     */
    public function test_the_application_cannot_create_todolist_item_without_todolist_id_when_store_called(): void
    {
        Event::fake();

        $name = fake()->realText(150);
        $description = random_int(0, 100) > 50 ? fake()->realTextBetween(2, 300) : null;

        $response = $this->post('/api/todolist-item', [
            'name' => $name,
            'description' => $description,
        ]);

        // response is successful
        $response->assertStatus(422);
        // response contains error with todolist_id key
        $response->assertJsonValidationErrors(['todolist_id']);
        // TodolistItemTransitioned event dispatched
        Event::assertNotDispatched(TodolistItemTransitioned::class);
    }

    /**
     * @return void
     * @throws RandomException
     */
    public function test_the_application_can_update_todolist_item_when_update_called(): void
    {
        Event::fake();

        /** @var TodolistItem|null $todolistItem */
        $todolistItem = TodolistItem::query()->inRandomOrder()->first();

        if (!$todolistItem) {
            throw new \RuntimeException('TodolistsItem not found, seeder probably didn\'t run');
        }

        $name = fake()->realText(150);
        $description = random_int(0, 100) > 50 ? fake()->realTextBetween(2, 300) : null;

        $response = $this->put('/api/todolist-item/' . $todolistItem->id, [
            'name' => $name,
            'description' => $description,
            'finished' => 1
        ]);

        // response is successful
        $response->assertStatus(200);
        // response returned todolistItem with name provided
        $this->assertEquals($name, $response->json('todolistItem.name'));
        // response returned todolistItem with description provided
        $this->assertEquals($description, $response->json('todolistItem.description'));
        // response todolistItem has been finished
        $this->assertNotNull($response->json('todolistItem.finished_at'));
        // TodolistItemUpdated event dispatched
        Event::assertDispatched(TodolistItemUpdated::class);
    }

    /**
     * @return void
     */
    public function test_the_application_cannot_update_todolist_id_in_todolist_item_when_update_called(): void
    {
        Event::fake();

        /** @var TodolistItem|null $todolistItem */
        $todolistItem = TodolistItem::query()->inRandomOrder()->first();
        /** @var TodolistItem|null $todolistItem2 */
        $todolistItem2 = TodolistItem::query()->inRandomOrder()->where('id', '!=', $todolistItem->id)->first();

        if (!$todolistItem || !$todolistItem2) {
            throw new \RuntimeException('TodolistsItem not found, seeder probably didn\'t run');
        }

        $response = $this->put('/api/todolist-item/' . $todolistItem->id, [
            'todolist_id' => $todolistItem2->id,
        ]);

        // response is successful
        $response->assertStatus(422);
        // response contains error with todolist_id key
        $response->assertJsonValidationErrors(['todolist_id']);
        // TodolistItemUpdated event not dispatched
        Event::assertNotDispatched(TodolistItemUpdated::class);
    }

    /**
     * @return void
     */
    public function test_the_application_can_delete_todolist_item_when_destroy_called(): void
    {
        /** @var TodolistItem|null $todolistItem */
        $todolistItem = TodolistItem::query()->inRandomOrder()->first();

        if (!$todolistItem) {
            throw new \RuntimeException('TodolistsItem not found, seeder probably didn\'t run');
        }

        $response = $this->delete('/api/todolist-item/' . $todolistItem->id);

        // response is successful
        $response->assertStatus(204);
        // todolistItem was deleted
        $this->assertModelMissing($todolistItem);
    }
}
