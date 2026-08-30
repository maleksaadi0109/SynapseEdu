<?php

use App\Http\Controllers\AssignmentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Assignment Routes
|--------------------------------------------------------------------------
*/

// Public or student viewable routes
Route::get('/courses/{course}/assignments', [AssignmentController::class, 'index']);
Route::get('/assignments/{assignment}', [AssignmentController::class, 'show']);

// Protected teacher / admin management routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/courses/{course}/assignments', [AssignmentController::class, 'store']);
    Route::put('/assignments/{assignment}', [AssignmentController::class, 'update']);
    Route::delete('/assignments/{assignment}', [AssignmentController::class, 'destroy']);
});
