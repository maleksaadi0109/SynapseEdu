<?php

namespace Database\Factories;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Teacher>
 */
class TeacherFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(['role' => 'teacher']),
            'teacher_number' => 'TEA-'.fake()->unique()->numberBetween(1000, 9999),
            'department' => fake()->word(),
            'specialization' => fake()->word(),
            'qualification' => 'B.Sc. '.fake()->word(),
            'school_name' => fake()->company().' High School',
            'bio' => fake()->sentence(),
            'sync_version' => 0,
        ];
    }
}
