<?php

namespace App\Http\Controllers;

use App\Action\Assignment\GetAssignmentsAction;
use App\Action\CreateAssignmentAction;
use App\Action\DeleteAssignmentAction;
use App\Action\UpdateAssignmentAction;
use App\Http\Requests\StoreAssignmentRequest;
use App\Http\Requests\UpdateAssignmentRequest;
use App\Http\Resources\AssignmentResource;
use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    /**
     * Display a listing of assignments for a course with optional search.
     */
    public function index(Request $request, Course $course, GetAssignmentsAction $action): JsonResponse
    {
        $this->authorize('view', $course);

        $assignments = $action->handle($course, $request->query('search'));

        return response()->json([
            'message' => 'Assignments retrieved successfully',
            'data' => AssignmentResource::collection($assignments),
        ], 200);
    }

    /**
     * Store a newly created assignment in storage.
     */
    public function store(StoreAssignmentRequest $request, CreateAssignmentAction $create, Course $course): JsonResponse
    {
        $assignment = $create->handle($request, $course);

        return response()->json([
            'message' => 'Assignment created successfully',
            'data' => new AssignmentResource($assignment),
        ], 201);
    }

    /**
     * Display the specified assignment.
     */
    public function show(Assignment $assignment): JsonResponse
    {
        $this->authorize('view', $assignment);

        $assignment->loadMissing(['course', 'lesson']);

        return response()->json([
            'message' => 'Assignment retrieved successfully',
            'data' => new AssignmentResource($assignment),
        ], 200);
    }

    /**
     * Update the specified assignment in storage.
     */
    public function update(UpdateAssignmentRequest $request, UpdateAssignmentAction $update, Assignment $assignment): JsonResponse
    {
        $updated = $update->handle($request, $assignment);

        return response()->json([
            'message' => 'Assignment updated successfully',
            'data' => new AssignmentResource($updated),
        ], 200);
    }

    /**
     * Remove the specified assignment from storage.
     */
    public function destroy(DeleteAssignmentAction $delete, Assignment $assignment): JsonResponse
    {
        $this->authorize('delete', $assignment);

        $delete->handle($assignment);

        return response()->json([
            'message' => 'Assignment deleted successfully',
        ], 200);
    }
}
