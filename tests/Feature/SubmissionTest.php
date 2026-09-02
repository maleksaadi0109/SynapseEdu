<?php

namespace Tests\Feature;

use App\Http\Resources\SubmissionResource;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Student;
use App\Models\Submission;
use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SubmissionTest extends TestCase
{
    use RefreshDatabase;

    /*
    |--------------------------------------------------------------------------
    | Create Submission Tests
    |--------------------------------------------------------------------------
    */

    public function test_student_can_submit_assignment(): void
    {
        $student = $this->LoginWithStudent();
        Sanctum::actingAs($student->user);

        $course = Course::factory()->create(['is_public' => true]);
        $assignment = Assignment::factory()->create([
            'course_id' => $course->id,
            'due_date' => now()->addDays(3),
        ]);

        $payload = [
            'content' => 'This is my comprehensive essay on the assignment topic.',
        ];

        $response = $this->postJson("/api/assignments/{$assignment->id}/submissions", $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => SubmissionResource::structure(),
            ])
            ->assertJsonPath('data.assignment_id', $assignment->id)
            ->assertJsonPath('data.student_id', $student->id)
            ->assertJsonPath('data.status', 'submitted');

        $this->assertDatabaseHas('submissions', [
            'assignment_id' => $assignment->id,
            'student_id' => $student->id,
            'status' => 'submitted',
        ]);
    }

    public function test_unauthenticated_user_cannot_submit(): void
    {
        $assignment = Assignment::factory()->create();

        $response = $this->postJson("/api/assignments/{$assignment->id}/submissions", [
            'content' => 'Some submission content.',
        ]);

        $response->assertStatus(401);
    }

    public function test_teacher_cannot_submit_assignment(): void
    {
        $teacher = $this->LoginWithTeacher();
        Sanctum::actingAs($teacher->user);

        $assignment = Assignment::factory()->create();

        $response = $this->postJson("/api/assignments/{$assignment->id}/submissions", [
            'content' => 'Teachers cannot submit assignments.',
        ]);

        $response->assertStatus(403);
    }

    public function test_cannot_submit_past_due_date(): void
    {
        $student = $this->LoginWithStudent();
        Sanctum::actingAs($student->user);

        $course = Course::factory()->create(['is_public' => true]);
        $assignment = Assignment::factory()->create([
            'course_id' => $course->id,
            'due_date' => now()->subDays(1), // Past due date
        ]);

        $response = $this->postJson("/api/assignments/{$assignment->id}/submissions", [
            'content' => 'Late submission that should be rejected.',
        ]);

        $response->assertStatus(403);
    }

    public function test_submission_requires_valid_content(): void
    {
        $student = $this->LoginWithStudent();
        Sanctum::actingAs($student->user);

        $course = Course::factory()->create(['is_public' => true]);
        $assignment = Assignment::factory()->create(['course_id' => $course->id]);

        $response = $this->postJson("/api/assignments/{$assignment->id}/submissions", [
            'content' => '', // Empty content
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['content']);
    }

    /*
    |--------------------------------------------------------------------------
    | Read Submission Tests
    |--------------------------------------------------------------------------
    */

    public function test_teacher_can_view_all_submissions_for_own_assignment(): void
    {
        $teacher = $this->LoginWithTeacher();
        Sanctum::actingAs($teacher->user);

        $course = Course::factory()->create(['teacher_id' => $teacher->id]);
        $assignment = Assignment::factory()->create(['course_id' => $course->id]);

        Submission::factory()->count(3)->create([
            'assignment_id' => $assignment->id,
        ]);

        $response = $this->getJson("/api/assignments/{$assignment->id}/submissions");

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_another_teacher_cannot_view_assignment_submissions(): void
    {
        $courseOwner = $this->LoginWithTeacher();
        $course = Course::factory()->create(['teacher_id' => $courseOwner->id]);
        $assignment = Assignment::factory()->create(['course_id' => $course->id]);

        $anotherTeacher = Teacher::factory()->create();
        Sanctum::actingAs($anotherTeacher->user);

        $response = $this->getJson("/api/assignments/{$assignment->id}/submissions");

        $response->assertStatus(403);
    }

    public function test_student_can_view_own_submission(): void
    {
        $student = $this->LoginWithStudent();
        Sanctum::actingAs($student->user);

        $submission = Submission::factory()->create([
            'student_id' => $student->id,
            'content' => 'My personal submission.',
        ]);

        $response = $this->getJson("/api/submissions/{$submission->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.content', 'My personal submission.');
    }

    public function test_student_cannot_view_another_students_submission(): void
    {
        $student1 = $this->LoginWithStudent();

        $student2 = Student::factory()->create();
        Sanctum::actingAs($student2->user);

        $submission = Submission::factory()->create([
            'student_id' => $student1->id,
        ]);

        $response = $this->getJson("/api/submissions/{$submission->id}");

        $response->assertStatus(403);
    }

    /*
    |--------------------------------------------------------------------------
    | Update & Delete Submission Tests
    |--------------------------------------------------------------------------
    */

    public function test_student_can_update_own_submission_before_deadline(): void
    {
        $student = $this->LoginWithStudent();
        Sanctum::actingAs($student->user);

        $assignment = Assignment::factory()->create(['due_date' => now()->addDays(5)]);
        $submission = Submission::factory()->create([
            'student_id' => $student->id,
            'assignment_id' => $assignment->id,
            'content' => 'Original content draft.',
            'status' => 'submitted',
            'sync_version' => 1,
        ]);

        $response = $this->putJson("/api/submissions/{$submission->id}", [
            'content' => 'Revised and improved submission content.',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.content', 'Revised and improved submission content.');

        $this->assertDatabaseHas('submissions', [
            'id' => $submission->id,
            'content' => 'Revised and improved submission content.',
            'sync_version' => 2,
        ]);
    }

    public function test_student_cannot_update_submission_if_already_graded(): void
    {
        $student = $this->LoginWithStudent();
        Sanctum::actingAs($student->user);

        $submission = Submission::factory()->graded()->create([
            'student_id' => $student->id,
        ]);

        $response = $this->putJson("/api/submissions/{$submission->id}", [
            'content' => 'Trying to edit after grading.',
        ]);

        $response->assertStatus(403);
    }

    public function test_student_can_delete_own_submission(): void
    {
        $student = $this->LoginWithStudent();
        Sanctum::actingAs($student->user);

        $submission = Submission::factory()->create([
            'student_id' => $student->id,
            'status' => 'submitted',
        ]);

        $response = $this->deleteJson("/api/submissions/{$submission->id}");

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Submission deleted successfully');

        $this->assertSoftDeleted('submissions', [
            'id' => $submission->id,
        ]);
    }
}
