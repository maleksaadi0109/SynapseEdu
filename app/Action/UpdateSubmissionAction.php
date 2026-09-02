<?php

namespace App\Action;

use App\Http\Requests\UpdateSubmissionRequest;
use App\Models\Submission;

class UpdateSubmissionAction
{
    /**
     * Update a student submission's content.
     */
    public function handle(UpdateSubmissionRequest $request, Submission $submission): Submission
    {
        $submission->update([
            'content' => $request->validated('content'),
            'sync_version' => ($submission->sync_version ?? 0) + 1,
        ]);

        return $submission;
    }
}
