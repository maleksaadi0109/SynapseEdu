<?php

namespace App\Action\Assignment;

use App\Models\Course;
use Illuminate\Database\Eloquent\Collection;

class GetAssignmentsAction
{
    /**
     * Retrieve all assignments for a course with optional keyword search.
     */
    public function handle(Course $course, ?string $search = null): Collection
    {
        $query = $course->assignments()->with(['lesson']);

        if (! empty(trim((string) $search))) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('instructions', 'like', "%{$search}%");
            });
        }

        return $query->latest()->get();
    }
}
