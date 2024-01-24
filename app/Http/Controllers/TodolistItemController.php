<?php

namespace App\Http\Controllers;

use App\Actions\TodolistItem\CreateOrUpdateTodolistItem;
use App\Actions\TodolistItem\DestroyTodolistItem;
use App\Actions\TodolistItem\GetTodolistItemPaginator;
use App\Http\Requests\TodolistItem\TodolistItemCreateRequest;
use App\Http\Requests\TodolistItem\TodolistItemRequest;
use App\Http\Requests\TodolistItem\TodolistItemUpdateRequest;
use App\Models\TodolistItem;
use Illuminate\Http\JsonResponse;

class TodolistItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param TodolistItemRequest $request
     * @return JsonResponse
     */
    public function index(TodolistItemRequest $request): JsonResponse
    {
        return response()->json([
            'paginator' => (new GetTodolistItemPaginator($request->validated()))->handle(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param TodolistItemCreateRequest $request
     * @return JsonResponse
     */
    public function store(TodolistItemCreateRequest $request): JsonResponse
    {
        $todolistItem = (new CreateOrUpdateTodolistItem(new TodolistItem(), $request->validated()))->handle();

        return response()->json([
            'todolistItem' => $todolistItem,
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param TodolistItem $todolistItem
     * @return JsonResponse
     */
    public function show(TodolistItem $todolistItem): JsonResponse
    {
        return response()->json([
            'todolistItem' => $todolistItem,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param TodolistItemUpdateRequest $request
     * @param TodolistItem $todolistItem
     * @return JsonResponse
     */
    public function update(TodolistItemUpdateRequest $request, TodolistItem $todolistItem): JsonResponse
    {
        $todolistItem = (new CreateOrUpdateTodolistItem($todolistItem, $request->validated()))->handle();

        return response()->json([
            'todolistItem' => $todolistItem,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param TodolistItem $todolistItem
     * @return JsonResponse
     */
    public function destroy(TodolistItem $todolistItem): JsonResponse
    {
        (new DestroyTodolistItem($todolistItem))->handle();

        return response()->json([], 204);
    }
}
