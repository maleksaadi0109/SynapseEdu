<?php

namespace App\Http\Controllers;

use App\Action\GetCourseAction;
use App\Action\GetCoursesAction;
use App\Course\CreateCourseAction;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Resources\CourseResource;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * List & search courses using Scout.
     */
    public function index(Request $request, GetCoursesAction $action)
    {
        $courses = $action->handle(
            search: $request->query('search'),
            teacherId: $request->query('teacher_id'),
            isPublic: $request->has('is_public') ? $request->boolean('is_public') : true,
            perPage: (int) $request->query('per_page', 15)
        );

        return CourseResource::collection($courses);
    }

    /**
     * Display a single course by ID or code.
     */
    public function show(string $id, GetCourseAction $action)
    {
        $course = $action->handle($id);

        return new CourseResource($course);
    }

    /**
     * Store a new course.
     */
    public function store(StoreCourseRequest $request, CreateCourseAction $action)
    {
        $course = $action->handle($request);

        return response()->json(
            [
                'message' => 'Course created successfully',
                'data' => new CourseResource($course),
            ],
            201
        );
    }
}
