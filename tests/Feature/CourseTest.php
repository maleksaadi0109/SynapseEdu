<?php

namespace Tests\Feature;

use App\Http\Resources\CourseResource;
use App\Models\Course;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    /*
    |--------------------------------------------------------------------------
    | Read & Search Tests
    |--------------------------------------------------------------------------
    */

    public function test_list_public_courses_only(): void
    {
        Course::factory()->count(5)->create(['is_public' => true]);
        Course::factory()->count(3)->create(['is_public' => false]);

        $response = $this->getJson('/api/courses');
        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    public function test_search_course_by_title(): void
    {
        Course::factory()->create(['title' => 'Introduction to Programming']);
        $matching = Course::factory()->create(['title' => 'Advanced Mathematics']);

        $response = $this->getJson('/api/courses?search=Advanced');
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $matching->id);
    }

    public function test_search_course_by_teacher_id(): void
    {
        $teacher = Teacher::factory()->create();
        $teacher2 = Teacher::factory()->create();
        Course::factory()->create(['teacher_id' => $teacher2->id]);
        $matchingCourse = Course::factory()->create(['teacher_id' => $teacher->id]);
        Course::factory()->count(2)->create();

        $response = $this->getJson('/api/courses?teacher_id='.$teacher->id);
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $matchingCourse->id);
    }

    public function test_search_course_by_code(): void
    {
        $course = Course::factory()->create([
            'code' => 'CS101',
        ]);

        $response = $this->getJson("/api/courses/{$course->code}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $course->id)
            ->assertJsonPath('data.code', 'CS101');
    }

    public function test_search_invaild_course_code(): void
    {
        $response = $this->getJson('/api/courses/INVALID_CODE');

        $response->assertStatus(404);
    }

    /*
    |--------------------------------------------------------------------------
    | Create Course Tests
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Update Course Tests
    |--------------------------------------------------------------------------
    */

    public function test_teacher_can_update_own_course(): void
    {
        $teacher = $this->LoginWithTeacher();
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'title' => 'Old Title',
            'sync_version' => 1,
        ]);

        Sanctum::actingAs($teacher->user);

        $payload = [
            'title' => 'Updated Course Title',
            'description' => 'Updated course description text.',
            'is_public' => false,
        ];

        $response = $this->putJson("/api/courses/{$course->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Updated Course Title')
            ->assertJsonPath('data.is_public', false);

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'title' => 'Updated Course Title',
            'sync_version' => 2,
        ]);
    }

    public function test_teacher_cannot_update_another_teachers_course(): void
    {
        $ownerTeacher = $this->LoginWithTeacher();
        $course = Course::factory()->create(['teacher_id' => $ownerTeacher->id]);

        $anotherTeacher = Teacher::factory()->create();
        Sanctum::actingAs($anotherTeacher->user);

        $response = $this->putJson("/api/courses/{$course->id}", [
            'title' => 'Hacked Title',
        ]);

        $response->assertStatus(403);
    }

    public function test_student_cannot_update_course(): void
    {
        $teacher = $this->LoginWithTeacher();
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);

        $student = Student::factory()->create();
        Sanctum::actingAs($student->user);

        $response = $this->putJson("/api/courses/{$course->id}", [
            'title' => 'Student Edit',
        ]);

        $response->assertStatus(403);
    }

    public function test_guest_cannot_update_course(): void
    {
        $course = Course::factory()->create();

        $response = $this->putJson("/api/courses/{$course->id}", [
            'title' => 'Guest Edit',
        ]);

        $response->assertStatus(401);
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Course Tests
    |--------------------------------------------------------------------------
    */

    public function test_teacher_can_delete_own_course(): void
    {
        $teacher = $this->LoginWithTeacher();
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);

        Sanctum::actingAs($teacher->user);

        $response = $this->deleteJson("/api/courses/{$course->id}");

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Course deleted successfully');

        $this->assertSoftDeleted('courses', [
            'id' => $course->id,
        ]);
    }

    public function test_teacher_cannot_delete_another_teachers_course(): void
    {
        $ownerTeacher = $this->LoginWithTeacher();
        $course = Course::factory()->create(['teacher_id' => $ownerTeacher->id]);

        $anotherTeacher = Teacher::factory()->create();
        Sanctum::actingAs($anotherTeacher->user);

        $response = $this->deleteJson("/api/courses/{$course->id}");

        $response->assertStatus(403);
    }

    public function test_student_cannot_delete_course(): void
    {
        $teacher = $this->LoginWithTeacher();
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);

        $student = Student::factory()->create();
        Sanctum::actingAs($student->user);

        $response = $this->deleteJson("/api/courses/{$course->id}");

        $response->assertStatus(403);
    }

    public function test_guest_cannot_delete_course(): void
    {
        $course = Course::factory()->create();

        $response = $this->deleteJson("/api/courses/{$course->id}");

        $response->assertStatus(401);
    }
}
