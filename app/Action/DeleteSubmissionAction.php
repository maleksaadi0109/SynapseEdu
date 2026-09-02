<?php

namespace App\Action;

use App\Models\Submission;

class DeleteSubmissionAction
{
    /**
     * Delete / withdraw a submission.
     */
    public function handle(Submission $submission): void
    {
        $submission->increment('sync_version');
        $submission->delete();
    }
}
