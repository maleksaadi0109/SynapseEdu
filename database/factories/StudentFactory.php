<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(['role' => 'student']),
            'student_number' => 'STU-'.fake()->unique()->numberBetween(10000, 99999),
            'grade_level' => (string) fake()->numberBetween(1, 12),
            'class_section' => fake()->randomElement(['A', 'B', 'C', 'D']),
            'school_name' => fake()->company().' High School',
            'birthday' => fake()->date('Y-m-d', '-15 years'),
            'guardian_name' => fake()->name(),
            'guardian_contact' => fake()->phoneNumber(),
            'sync_preference' => 'wifi_only',
            'sync_version' => 0,
            'learning_preference' => 'textual',
        ];
    }
}
