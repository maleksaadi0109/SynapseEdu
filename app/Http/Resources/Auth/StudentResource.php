<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
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
            'user_id' => $this->user_id,
            'student_number' => $this->student_number,
            'grade_level' => $this->grade_level,
            'class_section' => $this->class_section,
            'school_name' => $this->school_name,
            'birthday' => $this->birthday,
            'guardian_name' => $this->guardian_name,
            'guardian_contact' => $this->guardian_contact,
        ];
    }
}
