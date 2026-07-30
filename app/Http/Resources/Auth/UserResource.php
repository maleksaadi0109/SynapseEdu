<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'full_name' => $this->full_name,
            'email' => $this->email,
            'role' => $this->role,
            'avatar_path' => $this->avatar_path,
            'profile' => match ($this->role) {
                'student' => new StudentResource($this->whenLoaded('student')),
                'teacher' => new TeacherResource($this->whenLoaded('teacher')),
                default => null,
            },
        ];
    }
}
