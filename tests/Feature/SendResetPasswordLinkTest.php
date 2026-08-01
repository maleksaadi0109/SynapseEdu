<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendResetPasswordLinkTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_send_rest_password_link(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'malek@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'student',
        ]);

        $response = $this->postJson('/api/forgot-password', [
            'email' => 'malek@gmail.com']);

        $response->assertStatus(200)->assertJsonStructure([
            'message',
        ]);

        Notification::assertSentTo($user, ResetPasswordNotification::class);

    }

    public function test_fails_when_email_is_not_registered(): void
    {
        NOtification::fake();
        $response = $this->postJson('/api/forgot-password', [
            'email' => 'malek@gamil.com']);

        $response->assertStatus(422)->assertjsonValidationErrors([
            'email',
        ]);
        Notification::assertNothingSent();
    }
}
