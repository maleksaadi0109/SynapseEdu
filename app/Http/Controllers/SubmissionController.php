<?php

namespace App\Http\Controllers;

use App\Action\CreateSubmissionAction;
use App\Action\DeleteSubmissionAction;
use App\Action\GetSubmissionsAction;
use App\Action\UpdateSubmissionAction;
use App\Http\Requests\StoreSubmissionRequest;
use App\Http\Requests\UpdateSubmissionRequest;
use App\Http\Resources\SubmissionResource;
use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    /**
     * Display a listing of submissions for an assignment (Teacher).
     */
    public function index(Request $request, Assignment $assignment, GetSubmissionsAction $action): JsonResponse
    {
        $this->authorize('viewAny', [Submission::class, $assignment]);

        $submissions = $action->handle($assignment, $request->query('status'));

        return response()->json([
            'message' => 'Submissions retrieved successfully',
            'data' => SubmissionResource::collection($submissions),
        ], 200);
    }

    /**
     * Store a newly created submission in storage (Student).
     */
    public function store(StoreSubmissionRequest $request, Assignment $assignment, CreateSubmissionAction $create): JsonResponse
    {
        $submission = $create->handle($request, $assignment);

        return response()->json([
            'message' => 'Submission submitted successfully',
            'data' => new SubmissionResource($submission),
        ], 201);
    }

    /**
     * Display the specified submission (Student owner or Course Teacher).
     */
    public function show(Submission $submission): JsonResponse
    {
        $this->authorize('view', $submission);

        $submission->loadMissing(['assignment', 'student.user', 'essayEvaluation']);

        return response()->json([
            'message' => 'Submission retrieved successfully',
            'data' => new SubmissionResource($submission),
        ], 200);
    }

    /**
     * Update the specified submission in storage (Student owner).
     */
    public function update(UpdateSubmissionRequest $request, Submission $submission, UpdateSubmissionAction $update): JsonResponse
    {
        $updated = $update->handle($request, $submission);

        return response()->json([
            'message' => 'Submission updated successfully',
            'data' => new SubmissionResource($updated),
        ], 200);
    }

    /**
     * Remove the specified submission from storage.
     */
    public function destroy(Submission $submission, DeleteSubmissionAction $delete): JsonResponse
    {
        $this->authorize('delete', $submission);

        $delete->handle($submission);

        return response()->json([
            'message' => 'Submission deleted successfully',
        ], 200);
    }
}
