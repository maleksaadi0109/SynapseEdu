<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLessonRequest;
use App\Http\Resources\LessonResource;
use App\Lesson\CreateLessonAction;
use App\Models\Course;

class LessonController extends Controller
{
    public function store(StoreLessonRequest $request, Course $course, CreateLessonAction $createLessonAction)
    {

        $lesson = $createLessonAction->handle($request, $course);

        return response()->json([
            'message' => 'Lesson created successfully',
            'data' => new LessonResource($lesson),
        ], 201);
    }
}
