<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\lesson;
use App\Models\User;

class lessonPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, lesson $lesson): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Course $course): bool
    {
        if ($user->role === 'admin') {
            return true;
        }
        if ($user->role === 'teacher' && $user->teacher !== null) {
            return $user->teacher->id === $course->teacher_id;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, lesson $lesson): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, lesson $lesson): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, lesson $lesson): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, lesson $lesson): bool
    {
        return false;
    }
}
