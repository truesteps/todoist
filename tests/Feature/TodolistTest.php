<?php

namespace Tests\Feature;

use App\Events\Todolist\TodolistTransitioned;
use App\Events\Todolist\TodolistUpdated;
use App\Models\Todolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Random\RandomException;
use Tests\TestCase;

class TodolistTest extends TestCase
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
    public function test_the_application_returns_a_list_of_todolists_when_todolist_index_called(): void
    {
        $response = $this->get('/api/todolist');

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
    public function test_the_application_returns_todolist_with_todolist_item_paginator_when_todolist_show_called(): void
    {
        /** @var Todolist|null $todolist */
        $todolist = Todolist::query()->inRandomOrder()->first();

        if (!$todolist) {
            throw new \RuntimeException('Todolists not found, seeder probably didn\'t run');
        }

        $response = $this->get('/api/todolist/' . $todolist->id);

        // response is successful
        $response->assertStatus(200);
        // response returned paginator object
        $response->assertJson([
            'todolist' => [],
            'todolistItemPaginator' => [
                'data' => [],
            ]
        ]);
        // response returned some data
        $this->assertNotEmpty($response->json('todolistItemPaginator.data'));
    }

    /**
     * @return void
     */
    public function test_the_applications_returns_only_finished_todolist_items_when_todolist_show_called_with_finished_filter(
    ): void
    {
        /** @var Todolist|null $todolist */
        $todolist = Todolist::query()
            ->whereHas('todolist_items', function (Builder $query) {
                return $query->whereNotNull('finished_at');
            })
            ->inRandomOrder()
            ->first();

        if (!$todolist) {
            throw new \RuntimeException('Todolists not found, seeder probably didn\'t run');
        }

        $response = $this->get('/api/todolist/' . $todolist->id . '?finished=1');

        // response is successful
        $response->assertStatus(200);
        // response returned paginator object
        $response->assertJson([
            'todolist' => [],
            'todolistItemPaginator' => [
                'data' => [],
            ]
        ]);
        // response returned some data
        $this->assertNotEmpty($response->json('todolistItemPaginator.data'));

        // response only contains finished todolist items
        foreach ($response->json('todolistItemPaginator.data.*.finished_at') as $finishedAt) {
            $this->assertNotNull($finishedAt);
        }
    }

    /**
     * @return void
     */
    public function test_the_application_returns_a_filtered_list_of_todolist_items_when_search_query_provided(): void
    {
        /** @var Todolist|null $todolist */
        $todolist = Todolist::query()->inRandomOrder()->first();

        if (!$todolist) {
            throw new \RuntimeException('Todolists not found, seeder probably didn\'t run');
        }

        $response = $this->get('/api/todolist/' . $todolist->id);
        $response2 = $this->get('/api/todolist/' . $todolist->id . '?search=bow');

        // response is successful
        $response->assertStatus(200);
        // response returned paginator object
        $response->assertJson([
            'todolist' => [],
            'todolistItemPaginator' => [
                'data' => [],
            ]
        ]);
        // response is successful
        $response2->assertStatus(200);
        // response returned paginator object
        $response2->assertJson([
            'todolist' => [],
            'todolistItemPaginator' => [
                'data' => [],
            ]
        ]);
        // total count of todolist items should not be the same in both queries
        $this->assertNotEquals(
            $response->json('todolistItemPaginator.total'),
            $response2->json('todolistItemPaginator.total')
        );
    }

    /**
     * @return void
     */
    public function test_the_applications_doesnt_return_wrong_todolist_items_when_supplied_todolist_id_in_query(): void
    {
        /** @var Todolist|null $todolist */
        $todolist = Todolist::query()->inRandomOrder()->first();
        /** @var Todolist|null $todolist2 */
        $todolist2 = Todolist::query()->inRandomOrder()->where('id', '!=', $todolist->id)->first();

        if (!$todolist || !$todolist2) {
            throw new \RuntimeException('Todolists not found, seeder probably didn\'t run');
        }

        $response = $this->get('/api/todolist/' . $todolist->id . '?todolist_id=' . $todolist2->id);

        // response is successful
        $response->assertStatus(200);
        // response returned paginator object
        $response->assertJson([
            'todolist' => [],
            'todolistItemPaginator' => [
                'data' => [],
            ]
        ]);

        // response only contains todolist items that belong to originally queried todolist
        foreach ($response->json('todolistItemPaginator.data.*.todolist_id') as $todolistId) {
            $this->assertEquals($todolist->id, $todolistId);
        }
    }

    /**
     * @return void
     * @throws RandomException
     */
    public function test_the_application_can_create_todolist_when_store_called(): void
    {
        Event::fake();

        $name = fake()->realText(150);
        $description = random_int(0, 100) > 50 ? fake()->realTextBetween(2, 300) : null;

        $response = $this->post('/api/todolist', [
            'name' => $name,
            'description' => $description,
        ]);

        // response is successful
        $response->assertStatus(201);
        // response returned todolist object
        $response->assertJson([
            'todolist' => [],
        ]);
        // response returned todolist with name provided
        $this->assertEquals($name, $response->json('todolist.name'));
        // response returned todolist with description provided
        $this->assertEquals($description, $response->json('todolist.description'));
        // TodolistTransitioned event dispatched
        Event::assertDispatched(TodolistTransitioned::class);
    }

    /**
     * @return void
     */
    public function test_the_application_cannot_create_todolist_without_a_name_when_store_called(): void
    {
        Event::fake();

        $response = $this->post('/api/todolist');

        // response is successful
        $response->assertStatus(422);
        // response contains error with name key
        $response->assertJsonValidationErrors(['name']);
        // TodolistTransitioned event dispatched
        Event::assertNotDispatched(TodolistTransitioned::class);
    }

    /**
     * @return void
     * @throws RandomException
     */
    public function test_the_application_can_update_todolist_when_update_called(): void
    {
        Event::fake();

        /** @var Todolist|null $todolist */
        $todolist = Todolist::query()->inRandomOrder()->first();

        if (!$todolist) {
            throw new \RuntimeException('Todolists not found, seeder probably didn\'t run');
        }

        $name = fake()->realText(150);
        $description = random_int(0, 100) > 50 ? fake()->realTextBetween(2, 300) : null;

        $response = $this->put('/api/todolist/' . $todolist->id, [
            'name' => $name,
            'description' => $description,
        ]);

        // response is successful
        $response->assertStatus(200);
        // response returned todolist with name provided
        $this->assertEquals($name, $response->json('todolist.name'));
        // response returned todolist with description provided
        $this->assertEquals($description, $response->json('todolist.description'));
        // TodolistUpdated event dispatched
        Event::assertDispatched(TodolistUpdated::class);
    }

    /**
     * @return void
     */
    public function test_the_application_can_delete_todolist_when_destroy_called(): void
    {
        Event::fake();

        /** @var Todolist|null $todolist */
        $todolist = Todolist::query()->inRandomOrder()->first();

        if (!$todolist) {
            throw new \RuntimeException('Todolists not found, seeder probably didn\'t run');
        }

        $response = $this->delete('/api/todolist/' . $todolist->id);

        // response is successful
        $response->assertStatus(204);
        // todolist was soft-deleted
        $this->assertSoftDeleted($todolist);
        // TodolistTransitioned event dispatched
        Event::assertDispatched(TodolistTransitioned::class);
    }

    /**
     * @return void
     */
    public function test_the_application_cannot_delete_soft_deleted_todolist_when_destroy_called(): void
    {
        /** @var Todolist|null $todolist */
        $todolist = Todolist::query()->inRandomOrder()->first();

        if (!$todolist) {
            throw new \RuntimeException('Todolists not found, seeder probably didn\'t run');
        }

        $response = $this->delete('/api/todolist/' . $todolist->id);

        // response is successful
        $response->assertStatus(204);
        // todolist was soft-deleted
        $this->assertSoftDeleted($todolist);

        $response2 = $this->delete('/api/todolist/' . $todolist->id);

        // response is successful
        $response2->assertStatus(404);
    }
}
