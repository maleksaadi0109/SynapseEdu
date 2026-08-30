<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\User;

class AssignmentPolicy
{
    /**
     * Admin override: Grant all permissions to admins automatically.
     */
    public function before(?User $user, string $ability): ?bool
    {
        if ($user?->role === 'admin') {
            return true;
        }

        return null; // Proceed to specific methods
    }

    /**
     * Determine whether the user can view the assignment.
     */
    public function view(?User $user, Assignment $assignment): bool
    {
        // Public courses allow guests/students, or the teacher who owns the course
        return $assignment->course->is_public
            || $user?->teacher?->id === $assignment->course->teacher_id;
    }

    /**
     * Determine whether the user can create an assignment for a course.
     */
    public function create(User $user, Course $course): bool
    {
        return $user->role === 'teacher' && $user->teacher?->id === $course->teacher_id;
    }

    /**
     * Determine whether the user can update the assignment.
     */
    public function update(User $user, Assignment $assignment): bool
    {
        return $user->role === 'teacher' && $user->teacher?->id === $assignment->course->teacher_id;
    }

    /**
     * Determine whether the user can delete the assignment.
     */
    public function delete(User $user, Assignment $assignment): bool
    {
        return $user->role === 'teacher' && $user->teacher?->id === $assignment->course->teacher_id;
    }
}
