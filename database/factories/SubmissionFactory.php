<?php

namespace Database\Factories;

use App\Models\Assignment;
use App\Models\Student;
use App\Models\Submission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Submission>
 */
class SubmissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'assignment_id' => Assignment::factory(),
            'student_id' => Student::factory(),
            'content' => fake()->paragraphs(3, true),
            'submitted_at' => now(),
            'status' => 'submitted',
            'sync_version' => 1,
        ];
    }

    public function evaluating(): static
    {
        return $this->state(fn () => [
            'status' => 'evaluating',
        ]);
    }

    public function graded(): static
    {
        return $this->state(fn () => [
            'status' => 'graded',
        ]);
    }
}
