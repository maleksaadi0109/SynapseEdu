<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Resources\CourseResource;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreateCourseTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_course_successfully(): void
    {
        $teacher = $this->LoginWithTeacher();
        $payload = [
            'title' => 'Mathematics 101',
            'code' => 'MATH101',
            'description' => 'An introductory course to Mathematics.',
            'teacher_id' => $teacher->id,
        ];

        Sanctum::actingAs($teacher->user);
        $response = $this->postJson('/api/courses', $payload);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'data' => CourseResource::structure(),
        ]);

        $this->assertDatabaseHas('courses', [
            'title' => 'Mathematics 101',
            'code' => 'MATH101',
            'teacher_id' => $teacher->id,
        ]);
    }

    public function test_create_course_without_authentication(): void
    {
        $payload = [
            'title' => 'Mathematics 101',
            'code' => 'MATH101',
            'description' => 'An introductory course to Mathematics.',
            'teacher_id' => '01JK0000000000000000000000',
        ];

        $response = $this->postJson('/api/courses', $payload);
        $response->assertStatus(401);
    }

    public function test_create_course_with_invalid_role(): void
    {
        $student = $this->LoginWithStudent();
        $payload = [
            'title' => 'Mathematics 101',
            'code' => 'MATH101',
            'description' => 'An introductory course to Mathematics.',
            'teacher_id' => '01JK0000000000000000000000',
        ];

        Sanctum::actingAs($student->user);
        $response = $this->postJson('/api/courses', $payload);
        $response->assertStatus(403);
    }

    public function test_create_course_validation_fails_for_missing_required_fields(): void
    {
        $teacher = $this->LoginWithTeacher();

        Sanctum::actingAs($teacher->user);
        $response = $this->postJson('/api/courses', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title', 'description', 'code', 'teacher_id']);
    }

    public function test_create_course_validation_fails_for_invalid_teacher_id(): void
    {
        $teacher = $this->LoginWithTeacher();
        $payload = [
            'title' => 'Mathematics 101',
            'code' => 'MATH101',
            'description' => 'An introductory course to Mathematics.',
            'teacher_id' => '01NONEXISTENTTEACHERID0000',
        ];

        Sanctum::actingAs($teacher->user);
        $response = $this->postJson('/api/courses', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['teacher_id']);
    }


}
