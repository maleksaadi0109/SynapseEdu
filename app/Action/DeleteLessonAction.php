<?php

namespace App\Action;

use App\Models\Lesson;

class DeleteLessonAction
{
    /**
     * Create a new class instance.
     */
    public function handle(Lesson $lesson)
    {
        $lesson->increment('sync_version');
        $lesson->delete();
    }
}
