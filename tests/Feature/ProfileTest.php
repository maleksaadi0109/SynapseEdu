<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_student_profile()
    {
        $student = $this->LoginWithStudent();
        Sanctum::actingAs($student->user);

        $response = $this->getJson('/api/profile');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
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
            ]);
    }

    public function test_get_teacher_profile()
    {
        $teacher = $this->LoginWithTeacher();
        Sanctum::actingAs($teacher->user);

        $response = $this->getJson('/api/profile');

        $response->assertStatus(200)
            ->assertJsonStructure([
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

    public function test_get_info_without_authentication()
    {
        $response = $this->getJson('/api/profile');

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    public function test_update_student_profile()
    {
        $student = $this->LoginWithStudent();
        Sanctum::actingAs($student->user);

        $response = $this->putJson('/api/profile', [
            'full_name' => 'Updated Student Name',
            'grade_level' => '11',
            'school_name' => 'New Academy',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'full_name' => 'Updated Student Name',
                    'profile' => [
                        'grade_level' => '11',
                        'school_name' => 'New Academy',
                    ],
                ],
            ]);
    }

    public function test_update_teacher_profile()
    {
        $teacher = $this->LoginWithTeacher();
        Sanctum::actingAs($teacher->user);

        $response = $this->putJson('/api/profile', [
            'full_name' => 'Updated Teacher Name',
            'department' => 'Physics',
            'bio' => 'Experienced physics teacher.',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'full_name' => 'Updated Teacher Name',
                    'profile' => [
                        'department' => 'Physics',
                        'bio' => 'Experienced physics teacher.',
                    ],
                ],
            ]);
    }

    public function test_update_profile_without_authentication()
    {
        $response = $this->putJson('/api/profile', [
            'full_name' => 'Updated Name',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }
}
