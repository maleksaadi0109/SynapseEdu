<?php

namespace Tests\Feature;

use App\Http\Resources\LessonResource;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LessonTest extends TestCase
{
    use RefreshDatabase;

    /*
    |--------------------------------------------------------------------------
    | Read & Search Tests (Index, Show, Search)
    |--------------------------------------------------------------------------
    */

    public function test_can_list_lessons_for_a_course(): void
    {
        $course = Course::factory()->create();
        Lesson::factory()->count(3)->create(['course_id' => $course->id]);

        $response = $this->getJson("/api/courses/{$course->id}/lessons");

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_get_a_single_lesson(): void
    {
        $course = Course::factory()->create();
        $lesson = Lesson::factory()->create(['course_id' => $course->id]);

        $response = $this->getJson("/api/lessons/{$lesson->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => LessonResource::structure(),
            ]);
    }

    public function test_can_search_lessons_by_keyword(): void
    {
        $course = Course::factory()->create();

        Lesson::factory()->create([
            'course_id' => $course->id,
            'title' => 'Introduction to Algebra',
        ]);
        Lesson::factory()->create([
            'course_id' => $course->id,
            'title' => 'Advanced Geometry',
        ]);

        $response = $this->getJson("/api/courses/{$course->id}/lessons?search=Algebra");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Introduction to Algebra');
    }

    public function test_returns_404_for_invalid_lesson(): void
    {
        $response = $this->getJson('/api/lessons/non-existent-lesson-id');

        $response->assertStatus(404);
    }

    /*
    |--------------------------------------------------------------------------
    | Create Lesson Tests
    |--------------------------------------------------------------------------
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
            'order_index' => -5,
        ];

        $response = $this->postJson("/api/courses/{$course->id}/lessons", $payload);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['order_index']);
    }

    public function test_student_can_not_create_lesson(): void
    {
        $teacher = $this->LoginWithTeacher();
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
        ]);

        $student = Student::factory()->create();
        Sanctum::actingAs($student->user);

        $payload = [
            'course_id' => $course->id,
            'title' => 'Introduction to Algebra',
            'content' => 'This lesson covers the basics of algebra.',
            'order_index' => 1,
        ];

        $response = $this->postJson("/api/courses/{$course->id}/lessons", $payload);
        $response->assertStatus(403);
    }

    public function test_create_lesson_with_invalid_course(): void
    {
        $teacher = $this->LoginWithTeacher();
        Sanctum::actingAs($teacher->user);

        $payload = [
            'course_id' => 9999,
            'title' => 'Introduction to Algebra',
            'content' => 'This lesson covers the basics of algebra.',
            'order_index' => 1,
        ];

        $response = $this->postJson('/api/courses/9999/lessons', $payload);
        $response->assertStatus(404);
    }

    /*
    |--------------------------------------------------------------------------
    | Update Lesson Tests
    |--------------------------------------------------------------------------
    */

    public function test_teacher_can_update_own_lesson(): void
    {
        $teacher = $this->LoginWithTeacher();
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);
        $lesson = Lesson::factory()->create([
            'course_id' => $course->id,
            'title' => 'Old Title',
            'sync_version' => 1,
        ]);

        Sanctum::actingAs($teacher->user);

        $payload = [
            'title' => 'Updated Lesson Title',
            'content' => 'Updated content text here.',
            'order_index' => 5,
        ];

        $response = $this->putJson("/api/lessons/{$lesson->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Updated Lesson Title')
            ->assertJsonPath('data.order_index', 5);

        $this->assertDatabaseHas('lessons', [
            'id' => $lesson->id,
            'title' => 'Updated Lesson Title',
            'sync_version' => 2,
        ]);
    }

    public function test_teacher_cannot_update_another_teachers_lesson(): void
    {
        $ownerTeacher = $this->LoginWithTeacher();
        $course = Course::factory()->create(['teacher_id' => $ownerTeacher->id]);
        $lesson = Lesson::factory()->create(['course_id' => $course->id]);

        $anotherTeacher = Teacher::factory()->create();
        Sanctum::actingAs($anotherTeacher->user);

        $response = $this->putJson("/api/lessons/{$lesson->id}", [
            'title' => 'Hacked Title',
        ]);

        $response->assertStatus(403);
    }

    public function test_student_cannot_update_lesson(): void
    {
        $teacher = $this->LoginWithTeacher();
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);
        $lesson = Lesson::factory()->create(['course_id' => $course->id]);

        $student = Student::factory()->create();
        Sanctum::actingAs($student->user);

        $response = $this->putJson("/api/lessons/{$lesson->id}", [
            'title' => 'Student Edit',
        ]);

        $response->assertStatus(403);
    }

    public function test_guest_cannot_update_lesson(): void
    {
        $course = Course::factory()->create();
        $lesson = Lesson::factory()->create(['course_id' => $course->id]);

        $response = $this->putJson("/api/lessons/{$lesson->id}", [
            'title' => 'Guest Edit',
        ]);

        $response->assertStatus(401);
    }

    public function test_update_lesson_invalid_order_index(): void
    {
        $teacher = $this->LoginWithTeacher();
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);
        $lesson = Lesson::factory()->create(['course_id' => $course->id]);

        Sanctum::actingAs($teacher->user);

        $response = $this->putJson("/api/lessons/{$lesson->id}", [
            'order_index' => -10,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['order_index']);
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Lesson Tests
    |--------------------------------------------------------------------------
    */

    public function test_teacher_can_delete_own_lesson(): void
    {
        $teacher = $this->LoginWithTeacher();
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);
        $lesson = Lesson::factory()->create(['course_id' => $course->id]);

        Sanctum::actingAs($teacher->user);

        $response = $this->deleteJson("/api/lessons/{$lesson->id}");

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Lesson deleted successfully');

        $this->assertSoftDeleted('lessons', [
            'id' => $lesson->id,
        ]);
    }

    public function test_teacher_cannot_delete_another_teachers_lesson(): void
    {
        $ownerTeacher = $this->LoginWithTeacher();
        $course = Course::factory()->create(['teacher_id' => $ownerTeacher->id]);
        $lesson = Lesson::factory()->create(['course_id' => $course->id]);

        $anotherTeacher = Teacher::factory()->create();
        Sanctum::actingAs($anotherTeacher->user);

        $response = $this->deleteJson("/api/lessons/{$lesson->id}");

        $response->assertStatus(403);
    }

    public function test_student_cannot_delete_lesson(): void
    {
        $teacher = $this->LoginWithTeacher();
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);
        $lesson = Lesson::factory()->create(['course_id' => $course->id]);

        $student = Student::factory()->create();
        Sanctum::actingAs($student->user);

        $response = $this->deleteJson("/api/lessons/{$lesson->id}");

        $response->assertStatus(403);
    }

    public function test_guest_cannot_delete_lesson(): void
    {
        $course = Course::factory()->create();
        $lesson = Lesson::factory()->create(['course_id' => $course->id]);

        $response = $this->deleteJson("/api/lessons/{$lesson->id}");

        $response->assertStatus(401);
    }
}
