<?php

namespace App\Http\Controllers;

use App\Actions\Todolist\CreateOrUpdateTodolist;
use App\Actions\Todolist\DestroyTodolist;
use App\Actions\Todolist\GetTodolistPaginator;
use App\Http\Requests\Todolist\TodolistCreateRequest;
use App\Http\Requests\Todolist\TodolistRequest;
use App\Models\Todolist;
use Illuminate\Http\JsonResponse;

class TodolistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param TodolistRequest $request
     * @return JsonResponse
     */
    public function index(TodolistRequest $request): JsonResponse
    {
        return response()->json([
            'paginator' => (new GetTodolistPaginator($request->validated()))->handle(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param TodolistCreateRequest $request
     * @return JsonResponse
     */
    public function store(TodolistCreateRequest $request): JsonResponse
    {
        $todolist = (new CreateOrUpdateTodolist(new Todolist(), $request->validated()))->handle();

        return response()->json([
            'todolist' => $todolist,
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Todolist $todolist
     * @return JsonResponse
     */
    public function show(Todolist $todolist): JsonResponse
    {
        return response()->json([
            'todolist' => $todolist,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param TodolistCreateRequest $request
     * @param Todolist $todolist
     * @return JsonResponse
     */
    public function update(TodolistCreateRequest $request, Todolist $todolist): JsonResponse
    {
        $todolist = (new CreateOrUpdateTodolist($todolist, $request->validated()))->handle();

        return response()->json([
            'todolist' => $todolist,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Todolist $todolist
     * @return JsonResponse
     */
    public function destroy(Todolist $todolist): JsonResponse
    {
        (new DestroyTodolist($todolist))->handle();

        return response()->json([], 204);
    }
}
