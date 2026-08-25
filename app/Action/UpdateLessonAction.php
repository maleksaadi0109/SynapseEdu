<?php

namespace App\Action;

use App\Http\Requests\UpdateLessonRequest;
use App\Models\Lesson;

class UpdateLessonAction
{
    /**
     * Create a new class instance.
     */
    public function handle(UpdateLessonRequest $request, Lesson $lesson)
    {
        $lesson->update([
            'title' => $request->input('title', $lesson->title),
            'content' => $request->input('content', $lesson->content),
            'order_index' => $request->input('order_index', $lesson->order_index),
        ]);

        $lesson->increment('sync_version');

        return $lesson;
    }
}
