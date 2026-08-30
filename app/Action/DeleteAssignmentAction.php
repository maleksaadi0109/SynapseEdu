<?php

namespace App\Action;

use App\Models\Assignment;

class DeleteAssignmentAction
{
    /**
     * Create a new class instance.
     */
    public function handle(Assignment $assignment)
    {
        $assignment->increment('sync_version');
        $assignment->delete();
    }
}
