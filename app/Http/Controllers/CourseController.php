<?php

namespace App\Http\Controllers;

use App\Action\DeleteCourseAction;
use App\Action\GetCourseAction;
use App\Action\GetCoursesAction;
use App\Action\UpdateCourseAction;
use App\Course\CreateCourseAction;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Http\Resources\CourseResource;
use App\Models\Course;
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

    /**
     * Update an existing course.
     */
    public function update(UpdateCourseRequest $request, Course $course, UpdateCourseAction $action)
    {
        $course = $action->handle($request, $course);

        return response()->json(
            [
                'message' => 'Course updated successfully',
                'data' => new CourseResource($course),
            ],
            200
        );
    }

    /**
     * Soft delete a course.
     */
    public function destroy(Course $course, DeleteCourseAction $action)
    {
        $this->authorize('delete', $course);

        $action->handle($course);

        return response()->json(
            [
                'message' => 'Course deleted successfully',
            ],
            200
        );
    }
}
