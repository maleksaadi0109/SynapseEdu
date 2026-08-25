<?php

namespace App\Action;

use App\Models\Course;

class DeleteCourseAction
{
    /**
     * Soft delete a course and increment its sync version.
     */
    public function handle(Course $course): bool
    {
        $course->increment('sync_version');

        return $course->delete();
    }
}
