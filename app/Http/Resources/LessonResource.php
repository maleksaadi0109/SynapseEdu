<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
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
            'title' => $this->title,
            'content' => $this->content,
            'order_index' => $this->order_index,
            'sync_version' => $this->sync_version,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

        ];
    }

    public static function structure(): array
    {
        return [
            'id',
            'course_id',
            'title',
            'content',
            'order_index',
            'sync_version',
            'created_at',
            'updated_at',
        ];
    }
}
