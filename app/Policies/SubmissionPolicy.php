<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\Submission;
use App\Models\User;

class SubmissionPolicy
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
     * Determine whether the user can view all submissions for an assignment.
     */
    public function viewAny(User $user, Assignment $assignment): bool
    {
        return $user->role === 'teacher'
            && $user->teacher?->id === $assignment->course?->teacher_id;
    }

    /**
     * Determine whether the user can view the specific submission.
     */
    public function view(User $user, Submission $submission): bool
    {
        // Student who owns the submission
        if ($user->role === 'student' && $user->student?->id === $submission->student_id) {
            return true;
        }

        // Teacher who owns the course
        if ($user->role === 'teacher' && $user->teacher?->id === $submission->assignment?->course?->teacher_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create a submission for an assignment.
     */
    public function create(User $user, Assignment $assignment): bool
    {
        if ($user->role !== 'student' || ! $user->student) {
            return false;
        }

        // Must belong to an accessible / public course
        if (! $assignment->course?->is_public) {
            return false;
        }

        // Cannot submit past due date
        if ($assignment->due_date && now()->gt($assignment->due_date)) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can update the submission.
     */
    public function update(User $user, Submission $submission): bool
    {
        // Only student owner can edit their own submission
        if ($user->role !== 'student' || $user->student?->id !== $submission->student_id) {
            return false;
        }

        // Cannot update if already graded or currently evaluating
        if ($submission->status !== 'submitted') {
            return false;
        }

        // Cannot update past assignment due date
        if ($submission->assignment?->due_date && now()->gt($submission->assignment->due_date)) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can delete the submission.
     */
    public function delete(User $user, Submission $submission): bool
    {
        // Student owner can delete/withdraw before grading
        if ($user->role === 'student' && $user->student?->id === $submission->student_id && $submission->status === 'submitted') {
            return true;
        }

        // Teacher of the course can delete submission
        if ($user->role === 'teacher' && $user->teacher?->id === $submission->assignment?->course?->teacher_id) {
            return true;
        }

        return false;
    }
}
