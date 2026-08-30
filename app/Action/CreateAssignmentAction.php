<?php

namespace App\Action;

use App\Http\Requests\StoreAssignmentRequest;
use App\Models\Assignment;
use App\Models\Course;

class CreateAssignmentAction
{
    /**
     * Create a new class instance.
     */
    public function handle(StoreAssignmentRequest $request, Course $course)
    {
        return Assignment::create([
            'course_id' => $course->id,
            'lesson_id' => $request->input('lesson_id'),
            'title' => $request->input('title'),
            'instructions' => $request->input('instructions'),
            'due_date' => $request->input('due_date'),
            'max_score' => $request->input('max_score', 100),
            'sync_version' => 1,
        ]);

    }
}
