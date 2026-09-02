<?php

namespace App\Action;

use App\Http\Requests\StoreSubmissionRequest;
use App\Models\Assignment;
use App\Models\Submission;

class CreateSubmissionAction
{
    /**
     * Create a new student submission.
     */
    public function handle(StoreSubmissionRequest $request, Assignment $assignment): Submission
    {
        return Submission::create([
            'assignment_id' => $assignment->id,
            'student_id' => $request->user()->student->id,
            'content' => $request->validated('content'),
            'submitted_at' => now(),
            'status' => 'submitted',
            'sync_version' => 1,
        ]);
    }
}
