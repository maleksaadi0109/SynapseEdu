<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Faker\Factory;

class RegisterTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_create_student(): void
    {
        $payload = [
            'full_name' => 'John Doe',
            'email' => 'john.student@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'student',
            'grade_level' => 'Grade 10',
            'class_section' => '10-A',
            'school_name' => 'Greenwood High',
            'birthday' => '2008-05-15',
            'guardian_name' => 'Jane Doe',
            'guardian_contact' => '+1234567890',
        ];
        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'access_token',
            'token_type',
            'data' => [
                'id',
                'full_name',
                'email',
                'role',
                'avatar_path',
                'profile' => [
                    'id',
                    'user_id',
                    'grade_level',
                    'class_section',
                    'school_name',
                    'birthday',
                    'guardian_name',
                    'guardian_contact',
                ],
            ],
        ]);
    }

    public function test_create_teacher(): void
    {
        $payload = [
            'full_name' => 'Jane Smith',
            'email' => 'jane.teacher@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'teacher',
            'department' => 'Mathematics',
            'specialization' => 'Algebra',
            'qualification' => 'Master of Education',
            'school_name' => 'Greenwood High',
            'bio' => 'Experienced Math Teacher',
        ];
        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'access_token',
            'token_type',
            'data' => [
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
        ]);
    }

    public function test_register_with_existing_email(): void
    {

        User::factory()->create(['email' => 'existing@example.com']);
        $payload = [
            'full_name' => 'John Doe',
            'email' => 'existing@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'student',
            'grade_level' => 'Grade 10',
            'class_section' => '10-A',
            'school_name' => 'Greenwood High',
            'birthday' => '2008-05-15',
            'guardian_name' => 'Jane Doe',
            'guardian_contact' => '+1234567890',
        ];
        $response = $this->postJson('/api/register', $payload);

        $response->assertstatus(422);
        $response->assertJsonValidationErrors(['email']);
    }
}
