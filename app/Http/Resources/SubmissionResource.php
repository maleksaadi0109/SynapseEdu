<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubmissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'assignment_id' => $this->assignment_id,
            'student_id' => $this->student_id,
            'content' => $this->content,
            'status' => $this->status,
            'submitted_at' => $this->submitted_at?->toISOString() ?? $this->submitted_at,
            'sync_version' => (int) $this->sync_version,

            // Relationships when eager-loaded
            'assignment' => new AssignmentResource($this->whenLoaded('assignment')),
            'student' => $this->whenLoaded('student'),
            'evaluation' => $this->whenLoaded('essayEvaluation'),

            'created_at' => $this->created_at?->toISOString() ?? $this->created_at,
            'updated_at' => $this->updated_at?->toISOString() ?? $this->updated_at,
        ];
    }

    /**
     * Expected API JSON structure for tests / contracts.
     */
    public static function structure(): array
    {
        return [
            'id',
            'assignment_id',
            'student_id',
            'content',
            'status',
            'submitted_at',
            'sync_version',
            'created_at',
            'updated_at',
        ];
    }
}
