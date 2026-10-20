<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'expenses'], function() {
    Route::get('/', [ExpenseController::class, 'index']);
    Route::post('/', [ExpenseController::class, 'store']);
    Route::get('/stats', [ExpenseController::class, 'stats']);
});

Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}/expenses/summary', [UserController::class, 'summary']);

Route::get('/categories', [CategoryController::class, 'index']);
