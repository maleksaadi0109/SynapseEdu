<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Course & Lesson Management Routes
|--------------------------------------------------------------------------
*/

// Public course browsing routes
Route::get('/courses', [CourseController::class, 'index']);
Route::get('/courses/{id}', [CourseController::class, 'show']);

// Authenticated course creation & management
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/courses', [CourseController::class, 'store']);
    Route::post('/courses/{course}/lessons', [LessonController::class, 'store']);
});
