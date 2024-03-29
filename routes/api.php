<?php

use App\Http\Controllers\TodolistController;
use App\Http\Controllers\TodolistItemController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::controller(TodolistController::class)
    ->name('todolist.')
    ->prefix('todolist')
    ->group(function () {
        Route::get('/', 'index');
        Route::get('{todolist}', 'show');

        Route::post('/', 'store');

        Route::put('{todolist}', 'update');

        Route::delete('{todolist}', 'destroy');
    });

Route::controller(TodolistItemController::class)
    ->name('todolist-item.')
    ->prefix('todolist-item')
    ->group(function () {
        Route::get('/', 'index');
        Route::get('{todolistItem}', 'show');

        Route::post('/', 'store');

        Route::put('{todolistItem}', 'update');

        Route::delete('{todolistItem}', 'destroy');
    });
