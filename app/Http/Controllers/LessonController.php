<?php

namespace App\Http\Controllers;

use App\Action\DeleteLessonAction;
use App\Action\Lesson\GetLessonAction;
use App\Action\UpdateLessonAction;
use App\Http\Requests\StoreLessonRequest;
use App\Http\Requests\UpdateLessonRequest;
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

        $this->authorize('view', $course);
        $lessons = (new GetLessonAction)->handle($course, $request->query('search'));

        return response()->json([
            'message' => 'Lessons retrieved successfully',
            'data' => LessonResource::collection($lessons),
        ], 200);

    }

    public function show(Lesson $lesson)
    {
        $this->authorize('view', $lesson);

        return response()->json([
            'message' => 'Lesson retrieved successfully',
            'data' => new LessonResource($lesson),
        ], 200);
    }

    public function update(UpdateLessonRequest $request, Lesson $lesson)
    {

        $lesson = (new UpdateLessonAction)->handle($request, $lesson);

        return response()->json([
            'message' => 'Lesson updated successfully',
            'data' => new LessonResource($lesson),
        ], 200);

    }

    public function destroy(Lesson $lesson)
    {
        $this->authorize('delete', $lesson);

        (new DeleteLessonAction)->handle($lesson);

        return response()->json([
            'message' => 'Lesson deleted successfully',
        ], 200);
    }
}
