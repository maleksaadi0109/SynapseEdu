<?php

use App\Http\Controllers\SubmissionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Submission Management Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // List submissions for an assignment (Teacher) & Create a new submission (Student)
    Route::get('/assignments/{assignment}/submissions', [SubmissionController::class, 'index']);
    Route::post('/assignments/{assignment}/submissions', [SubmissionController::class, 'store']);

    // Single submission operations (Student/Teacher)
    Route::get('/submissions/{submission}', [SubmissionController::class, 'show']);
    Route::put('/submissions/{submission}', [SubmissionController::class, 'update']);
    Route::delete('/submissions/{submission}', [SubmissionController::class, 'destroy']);
});
