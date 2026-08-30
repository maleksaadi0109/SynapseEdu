<?php

namespace App\Action;

use App\Http\Requests\UpdateAssignmentRequest;
use App\Models\Assignment;

class UpdateAssignmentAction
{
    /**
     * Update an assignment.
     */
    public function handle(UpdateAssignmentRequest $request, Assignment $assignment): Assignment
    {
        $data = $request->validated();
        $data['sync_version'] = ($assignment->sync_version ?? 0) + 1;

        $assignment->update($data);

        return $assignment;
    }
}
