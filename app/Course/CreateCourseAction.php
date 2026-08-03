<?php

namespace App\Course;

use App\Models\User;
use App\Models\Course;
use App\Http\Requests\StoreCourseRequest;
use Illuminate\Session\Store;
use Illuminate\Support\Str;

class CreateCourseAction
{
    /**
     * Create a new class instance.
     */
    public function __construct() {}

    public function handle(StoreCourseRequest $request): Course
    {
        return    Course::create([
            'title' => $request->input('title'),
            'description' => $request->input('description') ?? null,
            'code' => $request->input('code') ?? strtoupper(Str::random(6)),
            'teacher_id' => $request->input('teacher_id'),
            'sync_version' => 1,
        ]);
    }
}
