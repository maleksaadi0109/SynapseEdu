<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'code' => $this->code,
            'is_public' => (bool) $this->is_public,
            'teacher_id' => $this->teacher_id,
            'sync_version' => $this->sync_version,
            'lessons' => LessonResource::collection($this->whenLoaded('lessons')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    public static function structure(bool $withLessons = false): array
    {
        $structure = [
            'id',
            'title',
            'description',
            'code',
            'is_public',
            'teacher_id',
            'sync_version',
            'created_at',
            'updated_at',
        ];

        if ($withLessons) {
            $structure[] = 'lessons';
        }

        return $structure;
    }
}
