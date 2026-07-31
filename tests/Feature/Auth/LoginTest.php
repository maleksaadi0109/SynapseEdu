<?php

namespace Tests\Feature\Auth;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_student(): void
    {
        $user = User::factory()->create([
            'email' => 'malek@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'student',
        ]);

        Student::create([
            'user_id' => $user->id,
            'student_number' => 'STU-1001',
            'grade_level' => 'Grade 10',
            'class_section' => '10-A',
            'school_name' => 'Greenwood High',
            'birthday' => '2008-05-15',
            'guardian_name' => 'Jane Doe',
            'guardian_contact' => '+1234567890',
        ]);

        $payload = [
            'email' => 'malek@gmail.com',
            'password' => 'password',
            'role' => 'student',
        ];

        $response = $this->postJson('/api/login', $payload);

        $response->assertStatus(200);
        if ($payload['role'] === 'student') {
            $response->assertJsonStructure($this->StudentResponse());
        } elseif ($payload['role'] === 'teacher') {
            $response->assertJsonStructure($this->TeacherResponse());
        }
    }

    public function test_login_teacher(): void
    {
        $user = User::factory()->create([
            'email' => 'malek@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'teacher',
        ]);

        Teacher::create([
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

        $response = $this->postJson('/api/login', $payload);

        $response->assertStatus(200);
        if ($payload['role'] === 'student') {
            $response->assertJsonStructure($this->StudentResponse());
        } elseif ($payload['role'] === 'teacher') {
            $response->assertJsonStructure($this->TeacherResponse());
        }
    }
}
