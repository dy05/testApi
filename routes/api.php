<?php

use App\Http\Controllers\Api\ExpenseController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'expenses'], function() {
    Route::get('/', [ExpenseController::class, 'index']);
    Route::post('/', [ExpenseController::class, 'store']);
    Route::get('/stats', [ExpenseController::class, 'stats']);
});
