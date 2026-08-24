<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\lesson;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<lesson>
 */
class LessonFactory extends Factory
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
            'title' => fake()->sentence(),
            'content' => fake()->paragraphs(3, true),
            'order_index' => fake()->numberBetween(1, 100),
            'sync_version' => 0,
        ];
    }
}
