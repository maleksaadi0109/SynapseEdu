<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentResource extends JsonResource
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
            'course_id' => $this->course_id,
            'lesson_id' => $this->lesson_id,
            'title' => $this->title,
            'instructions' => $this->instructions,
            'due_date' => $this->due_date?->toISOString() ?? $this->due_date,
            'max_score' => (int) $this->max_score,
            'sync_version' => (int) $this->sync_version,

            // Relationships when eager-loaded
            'course' => new CourseResource($this->whenLoaded('course')),
            'lesson' => new LessonResource($this->whenLoaded('lesson')),

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
            'course_id',
            'lesson_id',
            'title',
            'instructions',
            'due_date',
            'max_score',
            'sync_version',
            'created_at',
            'updated_at',
        ];
    }
}
