<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_Reset_Password(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'malek@gmail',
            'password' => Hash::make('password'),
        ]);

        $token = Password::createToken($user);

        $response = $this->postJson('/api/reset-password', [
            'email' => 'malek@gmail',
            'token' => $token,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertStatus(200)->assertJsonStructure([ 'message',]);
        $this->assertTrue(Hash::check('newpassword', $user->fresh()->password));
    }

    public function test_user_can_not_reset_password_with_invalid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'malek@gmail',
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/api/reset-password', [
            'email' => 'malek@gmail',
            'token' => 'invalid-token',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertStatus(422);
        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }


    public function test_fails_when_password_confirmation_does_not_match()
    {
        Notification::fake();
        User::factory()->create([
            'email' => 'malek@gmail',
            'password' => Hash::make('password'),
        ]);

        $token = Password::createToken(User::first());

        $response = $this->postJson('/api/reset-password', [
            'email' => 'malek@gmail',
            'token' => $token,
            'password' => 'newpassword',
            'password_confirmation' => 'differentpassword',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors([
            'password',
        ]);
    }
}
