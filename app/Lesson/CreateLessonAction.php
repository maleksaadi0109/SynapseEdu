<?php

namespace App\Lesson;

use App\Http\Requests\StoreLessonRequest;
use App\Models\Course;
use App\Models\Lesson;

class CreateLessonAction
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function handle(StoreLessonRequest $request, Course $course): Lesson
    {
        $lesson = Lesson::create([
            'course_id' => $course->id,
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'order_index' => $request->input('order_index'),
            'sync_version' => 1,
        ]);

        return $lesson;
    }
}
