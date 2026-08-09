<?php

namespace App\Actions\UserAction;

use App\Models\Teacher;

class UpdateTeacherAction
{
    public function handle(Teacher $teacher, array $data): Teacher
    {
        $teacherFields = array_intersect_key($data, array_flip([
            'department',
            'specialization',
            'qualification',
            'school_name',
            'bio',
        ]));

        if (! empty($teacherFields)) {
            $teacher->update($teacherFields);
        }

        return $teacher;
    }
}
