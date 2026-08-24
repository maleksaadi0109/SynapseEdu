<?php

namespace App\Action\Lesson;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Collection;

class GetLessonAction
{
    public function handle(Course $course, ?string $search = null): Collection
    {
        if (! empty(trim((string) $search))) {
            return Lesson::search($search)
                ->query(function ($query) use ($course) {
                    $query->where('course_id', $course->id);
                })
                ->get();
        }

        return $course->lessons()
            ->orderBy('order_index', 'asc')
            ->get();
    }
}
