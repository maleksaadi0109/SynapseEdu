<?php

namespace App\Policies;

use App\Models\User;

class CoursePolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function create(User $user): bool
    {
        return $user->role === 'teacher' || $user->role === 'admin';
    }

    public function update(Course $course)
    {
        if ($user->role === 'admin') {
            return true;
        }

        return ($user->role === 'teacher') && ($user->teacher->id === $course->teacher->id);

    }
}
