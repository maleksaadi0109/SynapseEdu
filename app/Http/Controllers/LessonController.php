<?php

namespace App\Http\Controllers;

use App\Action\Lesson\GetLessonAction;
use App\Http\Requests\StoreLessonRequest;
use App\Http\Resources\LessonResource;
use App\Lesson\CreateLessonAction;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;

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

    public function index(Request $request, Course $course)
    {
        $lessons = (new GetLessonAction)->handle($course, $request->query('search'));

        return response()->json([
            'message' => 'Lessons retrieved successfully',
            'data' => LessonResource::collection($lessons),
        ], 200);

    }

    public function show(Lesson $lesson)
    {
        return response()->json([
            'message' => 'Lesson retrieved successfully',
            'data' => new LessonResource($lesson),
        ], 200);
    }
}
