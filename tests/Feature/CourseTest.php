<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_public_courses_only()
    {

        Course::factory()->count(5)->create(['is_public' => true]);
        Course::factory()->count(3)->create(['is_public' => false]);

        $response = $this->getJson('/api/courses');
        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');

    }

    public function test_search_course_by_title()
    {
        Course::factory()->create(['title' => 'Introduction to Programming']);
        $matching = Course::factory()->create(['title' => 'Advanced Mathematics']);

        $reponse = $this->getJson('/api/courses?search=Advanced');
        $reponse->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $matching->id);

    }

    public function test_search_course_by_teacher_id()
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

    public function test_search_course_by_code()
    {

        $course = Course::factory()->create([
            'code' => 'CS101',
        ]);

        $response = $this->getJson("/api/courses/{$course->code}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $course->id)
            ->assertJsonPath('data.code', 'CS101');

    }

    public function test_search_invaild_course_code()
    {
        $response = $this->getJson('/api/courses/INVALID_CODE');

        $response->assertStatus(404);

    }
}
