<?php

namespace Tests;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
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

    public function LoginWithTeacher(): Teacher
    {
        $user = User::factory()->create([
            'email' => 'malek@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'teacher',
        ]);

        $teacher = Teacher::create([
            'user_id' => $user->id,
            'teacher_number' => 'TEA-2001',
            'department' => 'Mathematics',
            'specialization' => 'Algebra',
            'qualification' => 'M.Sc. in Mathematics',
            'school_name' => 'Greenwood High',
            'bio' => 'Passionate about teaching and inspiring students.',
        ]);

        $payload = [
            'email' => 'malek@gmail.com',
            'password' => 'password',
            'role' => 'teacher',
        ];

        $this->postJson('/api/login', $payload);

        return $teacher;

    }

    public function LoginWithStudent(): Student
    {
        $user = User::factory()->create([
            'email' => 'student@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'student',
        ]);
        $student = Student::create([
            'user_id' => $user->id,
            'student_number' => 'STU-1001',
            'grade_level' => '10',
            'class_section' => 'A',
            'school_name' => 'Greenwood High',
            'birthday' => '2005-05-15',
            'guardian_name' => 'John Doe',
            'guardian_contact' => '123-456-7890',
        ]);

        $payload = [
            'email' => 'student@gmail.com',
            'password' => 'password',
            'role' => 'student',
        ];

        $this->postJson('/api/login', $payload);

        return $student;
    }
}
