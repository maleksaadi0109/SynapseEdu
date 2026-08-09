<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdatePasswordTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * A basic feature test example.
     */
    public function test_update_teacher_password_test(): void
    {

        $teacher = $this->LoginWithTeacher();
        Sanctum::actingAs($teacher->user);
        $response = $this->putJson('/api/password', [
            'current_password' => 'password',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Password updated successfully',
            ]);
        $teacher->user->refresh();
        $this->assertTrue(Hash::check('newpassword', $teacher->user->password));
    }

    public function test_update_student_password_test(): void
    {

        $student = $this->LoginWithStudent();
        Sanctum::actingAs($student->user);
        $response = $this->putJson('/api/password', [
            'current_password' => 'password',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Password updated successfully',
            ]);
        $student->user->refresh();
        $this->assertTrue(Hash::check('newpassword', $student->user->password));
    }

    public function test_update_password_with_invalid_current_password(): void
    {
        $teacher = $this->LoginWithTeacher();
        Sanctum::actingAs($teacher->user);
        $response = $this->putJson('/api/password', [
            'current_password' => 'wrongpassword',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['current_password']);
    }

    public function test_update_password_with_mismatched_confirmation(): void
    {
        $teacher = $this->LoginWithTeacher();
        Sanctum::actingAs($teacher->user);

        $response = $this->putJson('/api/password', [
            'current_password' => 'password',
            'password' => 'newpassword',
            'password_confirmation' => 'different_password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_update_password_unauthenticated(): void
    {
        $response = $this->putJson('/api/password', [
            'current_password' => 'password',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }
}
