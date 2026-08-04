<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [RegisterController::class, 'register']);
Route::get('/register', function () {
    return response()->json(['message' => 'Register endpoint']);
});

Route::post('/login', [LoginController::class, 'login']);
Route::get('/login', function () {
    return response()->json(['message' => 'login endpoint']);
});

Route::post('/logout', [LogoutController::class, 'logout'])->middleware('auth:sanctum');

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.reset');

Route::post('/courses', [CourseController::class, 'store'])->middleware('auth:sanctum');

Route::post('/courses/{course}/lessons', [LessonController::class, 'store'])->middleware('auth:sanctum');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
