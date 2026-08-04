<?php

namespace Tests\Feature;

use App\Http\Resources\LessonResource;
use App\Models\Course;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreateLessonTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_create_lesson(): void
    {
        $teacher = $this->LoginWithTeacher();
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
        ]);
        Sanctum::actingAs($teacher->user);
        $payload = [
            'course_id' => $course->id,
            'title' => 'Introduction to Algebra',
            'content' => 'This lesson covers the basics of algebra.',
            'order_index' => 1,
        ];

        $response = $this->postJson("/api/courses/{$course->id}/lessons", $payload);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'data' => LessonResource::structure(),
        ]);
    }

    public function test_create_lesson_without_authentication(): void
    {
        $course = Course::factory()->create();
        $payload = [
            'course_id' => $course->id,
            'title' => 'Introduction to Algebra',
            'content' => 'This lesson covers the basics of algebra.',
            'order_index' => 1,
        ];

        $response = $this->postJson("/api/courses/{$course->id}/lessons", $payload);
        $response->assertStatus(401);
    }

    public function test_create_lesson_invalid_teacher(): void
    {
        $courseOwner = $this->LoginWithTeacher();
        $course = Course::factory()->create([
            'teacher_id' => $courseOwner->id,
        ]);

        $anotherTeacher = Teacher::factory()->create();
        Sanctum::actingAs($anotherTeacher->user);

        $payload = [
            'course_id' => $course->id,
            'title' => 'Introduction to Algebra',
            'content' => 'This lesson covers the basics of algebra.',
            'order_index' => 1,
        ];

        $response = $this->postJson("/api/courses/{$course->id}/lessons", $payload);
        $response->assertStatus(403);
    }

    public function test_create_lesson_required_fields(): void
    {
        $teacher = $this->LoginWithTeacher();
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
        ]);
        Sanctum::actingAs($teacher->user);

        $payload = [
            'course_id' => $course->id,
            // Missing title, content, and order_index
        ];

        $response = $this->postJson("/api/courses/{$course->id}/lessons", $payload);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title', 'content']);
    }

    public function test_create_lesson_invalid_order_index(): void
    {
        $teacher = $this->LoginWithTeacher();
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
        ]);
        Sanctum::actingAs($teacher->user);

        $payload = [
            'course_id' => $course->id,
            'title' => 'Introduction to Algebra',
            'content' => 'This lesson covers the basics of algebra.',
            'order_index' => -5, // Invalid order index
        ];

        $response = $this->postJson("/api/courses/{$course->id}/lessons", $payload);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['order_index']);
    }

    public function test_studet_can_not_create_lesson(): void
    {
        $teacher = $this->LoginWithTeacher();
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
        ]);

        // Create a student and authenticate as the student
        $student = Student::factory()->create();
        Sanctum::actingAs($student->user);

        $payload = [
            'course_id' => $course->id,
            'title' => 'Introduction to Algebra',
            'content' => 'This lesson covers the basics of algebra.',
            'order_index' => 1,
        ];

        $response = $this->postJson("/api/courses/{$course->id}/lessons", $payload);
        $response->assertStatus(403); // Students should not be able to create lessons
    }

    public function test_create_lesson_with_invalid_course(): void
    {
        $teacher = $this->LoginWithTeacher();
        Sanctum::actingAs($teacher->user);

        $payload = [
            'course_id' => 9999, // Assuming this course ID does not exist
            'title' => 'Introduction to Algebra',
            'content' => 'This lesson covers the basics of algebra.',
            'order_index' => 1,
        ];

        $response = $this->postJson('/api/courses/9999/lessons', $payload);
        $response->assertStatus(404); // Course not found
    }
}
