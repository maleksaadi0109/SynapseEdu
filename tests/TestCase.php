<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function StudentResponse(string $userKey = 'user'): array
    {
        return [
            $userKey => [
                'id',
                'full_name',
                'email',
                'role',
                'avatar_path',
                'profile' => [
                    'id',
                    'user_id',
                    'student_number',
                    'grade_level',
                    'class_section',
                    'school_name',
                    'birthday',
                    'guardian_name',
                    'guardian_contact',
                ],
            ],
            'access_token',
            'token_type',
        ];
    }

    public function TeacherResponse(string $userKey = 'user'): array
    {
        return [
            $userKey => [
                'id',
                'full_name',
                'email',
                'role',
                'avatar_path',
                'profile' => [
                    'id',
                    'user_id',
                    'teacher_number',
                    'department',
                    'specialization',
                    'qualification',
                    'school_name',
                    'bio',
                ],
            ],
            'access_token',
            'token_type',
        ];
    }
}
