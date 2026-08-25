<?php

namespace App\Action;

use App\Http\Requests\UpdateCourseRequest;
use App\Models\Course;

class UpdateCourseAction
{
    /**
     * Update an existing course and increment sync version.
     */
    public function handle(UpdateCourseRequest $request, Course $course): Course
    {
        $course->update([
            'title' => $request->input('title', $course->title),
            'description' => $request->input('description', $course->description),
            'code' => $request->input('code', $course->code),
            'is_public' => $request->input('is_public', $course->is_public),
        ]);

        $course->increment('sync_version');

        return $course;
    }
}
