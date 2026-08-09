<?php

namespace App\Actions\UserAction;

use App\Models\Student;

class UpdateStudentAction
{
    public function handle(Student $student, array $data): Student
    {
        $studentFields = array_intersect_key($data, array_flip([
            'grade_level',
            'class_section',
            'school_name',
            'birthday',
            'guardian_name',
            'guardian_contact',
        ]));

        if (! empty($studentFields)) {
            $student->update($studentFields);
        }

        return $student;
    }
}
