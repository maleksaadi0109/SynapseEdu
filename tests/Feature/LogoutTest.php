<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_logout_teacher(): void
    {
        $teacher = $this->LoginWithTeacher();
        Sanctum::actingAs($teacher->user);

        $response = $this->postJson('/api/logout');
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Logged out successfully',
        ]);
    }

    public function test_logout_student(): void
    {
        $student = $this->LoginWithStudent();
        Sanctum::actingAs($student->user);

        $response = $this->postJson('/api/logout');
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Logged out successfully',
        ]);
    }

    public function test_logout_without_authentication(): void
    {
        $response = $this->postJson('/api/logout');
        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Unauthenticated.',
        ]);
    }

    public function test_token_is_revoked_after_logout(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/logout');

        $response->assertStatus(200);

        Auth::forgetGuards();

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/user')
            ->assertStatus(401);
    }
}
