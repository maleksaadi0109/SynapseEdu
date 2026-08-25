<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;

class LessonPolicy
{
    /**
     * Admin override: Grant all permissions to admins automatically.
     */
    public function before(?User $user, string $ability): ?bool
    {
        if ($user?->role === 'admin') {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view the lesson.
     */
    public function view(?User $user, Lesson $lesson): bool
    {
        return $lesson->course->is_public
            || $user?->teacher?->id === $lesson->course->teacher_id;
    }

    /**
     * Determine whether the user can create lessons for a course.
     */
    public function create(User $user, Course $course): bool
    {
        return $user->role === 'teacher' && $user->teacher?->id === $course->teacher_id;
    }

    /**
     * Determine whether the user can update the lesson.
     */
    public function update(User $user, Lesson $lesson): bool
    {
        return $user->role === 'teacher' && $user->teacher?->id === $lesson->course->teacher_id;
    }

    /**
     * Determine whether the user can delete the lesson.
     */
    public function delete(User $user, Lesson $lesson): bool
    {
        return $user->role === 'teacher' && $user->teacher?->id === $lesson->course->teacher_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Lesson $lesson): bool
    {
        return $user->role === 'teacher' && $user->teacher?->id === $lesson->course->teacher_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Lesson $lesson): bool
    {
        return false;
    }
}
