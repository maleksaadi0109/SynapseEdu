<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'teacher_id' => Teacher::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'code' => strtoupper(fake()->bothify('???-###')),
            'sync_version' => 0,
        ];
    }
}
