<?php

namespace Tests\Feature;

use App\Http\Resources\LessonResource;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetLessonTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
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
                'data' => LessonResource::structure($lesson),
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
}
