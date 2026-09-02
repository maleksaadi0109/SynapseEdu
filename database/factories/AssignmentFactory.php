<?php

namespace Database\Factories;

use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Assignment>
 */
class AssignmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'lesson_id' => null,
            'title' => fake()->sentence(4),
            'instructions' => fake()->paragraph(2),
            'due_date' => now()->addDays(7),
            'max_score' => 100,
            'sync_version' => 1,
        ];
    }
}
