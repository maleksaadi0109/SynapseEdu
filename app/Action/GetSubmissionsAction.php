<?php

namespace App\Action;

use App\Models\Assignment;
use Illuminate\Database\Eloquent\Collection;

class GetSubmissionsAction
{
    /**
     * Retrieve all submissions for an assignment.
     */
    public function handle(Assignment $assignment, ?string $status = null): Collection
    {
        $query = $assignment->submissions()
            ->with(['student.user', 'essayEvaluation']);

        if ($status && in_array($status, ['submitted', 'evaluating', 'graded'], true)) {
            $query->where('status', $status);
        }

        return $query->latest('submitted_at')->get();
    }
}
