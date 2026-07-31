<?php

namespace App\Actions\AuthAction;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\UserResource as AuthUserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginAction
{
    /**
     * Create a new class instance.
     */
    public function __construct() {}

    public function handle(LoginRequest $request): array
    {
        $credentials = $request->only('email', 'password');
        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = Auth::user();
        $role = strtolower((string) $request->input('role'));
        if ($user->role !== $role) {
            throw ValidationException::withMessages([
                'role' => ['The provided role does not match the user role.'],
            ]);
        }
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => new AuthUserResource($user->load($role)),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }
}
