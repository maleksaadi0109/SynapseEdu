<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseRequest;
use App\Course\CreateCourseAction;
use App\Http\Resources\CourseResource;

class CourseController extends Controller
{
    public function store(StoreCourseRequest $request, CreateCourseAction $action)
    {
        $course = $action->handle($request);
        return response()->json(
            ['message' => 'Course created successfully',
                'data' => new CourseResource($course)],
            201
        );

    }
}
