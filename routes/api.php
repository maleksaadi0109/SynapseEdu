<?php

use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;

Route::post('/register', [RegisterController::class, 'register']);
Route::get('/register', function () {
    return response()->json(['message' => 'Register endpoint']);
});
Route::post('/login', [LoginController::class, 'login']);
Route::get('/login', function () {
    return response()->json(['message' => 'login endpoint']);
});

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.reset');

Route::post('/courses', [CourseController::class, 'store'])->middleware('auth:sanctum');

Route::post('/courses/{course}/lessons', [LessonController::class, 'store'])->middleware('auth:sanctum');

Route::get('/user', function (Request $request) {

    return $request->user();
})->middleware('auth:sanctum');
