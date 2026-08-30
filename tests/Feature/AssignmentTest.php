<?php

namespace Tests\Feature;

use App\Http\Resources\AssignmentResource;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AssignmentTest extends TestCase
{
    use RefreshDatabase;

    /*
    |--------------------------------------------------------------------------
    | Create Assignment Tests
    |--------------------------------------------------------------------------
    */

    public function test_can_create_new_assignment(): void
    {
        $teacher = $this->LoginWithTeacher();
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
        ]);
        $lesson = Lesson::factory()->create([
            'course_id' => $course->id,
        ]);

        Sanctum::actingAs($teacher->user);

        $payload = [
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
            'title' => 'Test Assignment',
            'instructions' => 'This is a test assignment.',
            'due_date' => now()->addDays(7)->toDateTimeString(),
            'max_score' => 100,
        ];

        $response = $this->postJson("/api/courses/{$course->id}/assignments", $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => AssignmentResource::structure(),
            ])
            ->assertJsonPath('data.title', 'Test Assignment')
            ->assertJsonPath('data.course_id', $course->id);

        $this->assertDatabaseHas('assignments', [
            'course_id' => $course->id,
            'title' => 'Test Assignment',
        ]);
    }

    public function test_unauthenticated_cannot_create_assignment(): void
    {
        $course = Course::factory()->create();

        $payload = [
            'title' => 'Unauthenticated Assignment',
            'instructions' => 'Some instructions',
            'due_date' => now()->addDays(5)->toDateTimeString(),
        ];

        $response = $this->postJson("/api/courses/{$course->id}/assignments", $payload);

        $response->assertStatus(401);
    }

    public function test_teacher_cannot_create_assignment_for_another_teachers_course(): void
    {
        $courseOwner = $this->LoginWithTeacher();
        $course = Course::factory()->create([
            'teacher_id' => $courseOwner->id,
        ]);

        $anotherTeacher = Teacher::factory()->create();
        Sanctum::actingAs($anotherTeacher->user);

        $payload = [
            'title' => 'Unauthorized Assignment',
            'instructions' => 'Should fail with 403',
            'due_date' => now()->addDays(5)->toDateTimeString(),
        ];

        $response = $this->postJson("/api/courses/{$course->id}/assignments", $payload);

        $response->assertStatus(403);
    }

    public function test_create_assignment_validation_errors(): void
    {
        $teacher = $this->LoginWithTeacher();
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
        ]);
        Sanctum::actingAs($teacher->user);

        $response = $this->postJson("/api/courses/{$course->id}/assignments", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'due_date']);
    }

    /*
    |--------------------------------------------------------------------------
    | Read Assignment Tests
    |--------------------------------------------------------------------------
    */

    public function test_can_list_assignments_for_course(): void
    {
        $course = Course::factory()->create();
        Assignment::create([
            'course_id' => $course->id,
            'title' => 'Assignment 1',
            'instructions' => 'Instructions 1',
            'due_date' => now()->addDays(3),
            'max_score' => 100,
            'sync_version' => 1,
        ]);
        Assignment::create([
            'course_id' => $course->id,
            'title' => 'Assignment 2',
            'instructions' => 'Instructions 2',
            'due_date' => now()->addDays(5),
            'max_score' => 100,
            'sync_version' => 1,
        ]);

        $response = $this->getJson("/api/courses/{$course->id}/assignments");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_can_search_assignments_by_keyword(): void
    {
        $course = Course::factory()->create();
        Assignment::create([
            'course_id' => $course->id,
            'title' => 'Physics Homework',
            'instructions' => 'Newton laws',
            'due_date' => now()->addDays(3),
            'max_score' => 100,
            'sync_version' => 1,
        ]);
        Assignment::create([
            'course_id' => $course->id,
            'title' => 'Literature Essay',
            'instructions' => 'Shakespeare analysis',
            'due_date' => now()->addDays(5),
            'max_score' => 100,
            'sync_version' => 1,
        ]);

        $response = $this->getJson("/api/courses/{$course->id}/assignments?search=Physics");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Physics Homework');
    }

    public function test_can_get_single_assignment(): void
    {
        $course = Course::factory()->create(['is_public' => true]);
        $assignment = Assignment::create([
            'course_id' => $course->id,
            'title' => 'Single Assignment',
            'instructions' => 'View single assignment',
            'due_date' => now()->addDays(3),
            'max_score' => 50,
            'sync_version' => 1,
        ]);

        $response = $this->getJson("/api/assignments/{$assignment->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Single Assignment')
            ->assertJsonPath('data.max_score', 50);
    }

    /*
    |--------------------------------------------------------------------------
    | Update & Delete Assignment Tests
    |--------------------------------------------------------------------------
    */

    public function test_teacher_can_update_own_assignment(): void
    {
        $teacher = $this->LoginWithTeacher();
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);
        $assignment = Assignment::create([
            'course_id' => $course->id,
            'title' => 'Original Title',
            'instructions' => 'Original instructions',
            'due_date' => now()->addDays(3),
            'max_score' => 100,
            'sync_version' => 1,
        ]);

        Sanctum::actingAs($teacher->user);

        $response = $this->putJson("/api/assignments/{$assignment->id}", [
            'title' => 'Updated Assignment Title',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Updated Assignment Title');

        $this->assertDatabaseHas('assignments', [
            'id' => $assignment->id,
            'title' => 'Updated Assignment Title',
            'sync_version' => 2,
        ]);
    }

    public function test_teacher_can_delete_own_assignment(): void
    {
        $teacher = $this->LoginWithTeacher();
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);
        $assignment = Assignment::create([
            'course_id' => $course->id,
            'title' => 'To Delete',
            'due_date' => now()->addDays(2),
            'max_score' => 100,
            'sync_version' => 1,
        ]);

        Sanctum::actingAs($teacher->user);

        $response = $this->deleteJson("/api/assignments/{$assignment->id}");

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Assignment deleted successfully');

        $this->assertSoftDeleted('assignments', [
            'id' => $assignment->id,
        ]);
    }
}
