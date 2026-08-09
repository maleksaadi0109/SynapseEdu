<?php

namespace App\Action;

use App\Models\Course;

class GetCourseAction
{
    /**
     * Retrieve a single course by its ULID or unique course code.
     *
     * @param  string  $idOrCode  Course ULID or unique code (e.g. "CS101")
     */
    public function handle(string $idOrCode): Course
    {
        return Course::query()
            ->with([
                'teacher.user',
                'lessons' => fn ($query) => $query->latest('id'),
                'assignments',
            ])
            ->withCount(['lessons', 'assignments'])
            ->where('id', $idOrCode)
            ->orWhere('code', $idOrCode)
            ->firstOrFail();
    }
}
