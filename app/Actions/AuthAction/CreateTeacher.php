<?php

namespace App\Actions\AuthAction;

use App\Http\Requests\Auth\RegisterTeacherRequest;
use App\Http\Resources\Auth\UserResource as AuthUserResource;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateTeacher
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function handle(RegisterTeacherRequest $request): array
    {
        $user = DB::transaction(function () use ($request) {
            $avatarPath = null;
            if ($request->hasFile('avatar_path')) {
                $avatarPath = $request->file('avatar_path')->store('avatars', 'public');
            } elseif (is_string($request->input('avatar_path'))) {
                $avatarPath = $request->input('avatar_path');
            }

            $user = User::create([
                'full_name' => $request->input('full_name'),
                'email' => $request->input('email'),
                'password' => bcrypt($request->input('password')),
                'role' => 'teacher',
                'avatar_path' => $avatarPath,
            ]);

            Teacher::create([
                'user_id' => $user->id,
                'teacher_number' => $request->input('teacher_number') ?? ('TCH-'.strtoupper(Str::random(8))),
                'department' => $request->input('department'),
                'specialization' => $request->input('specialization') ?? $request->input('subject'),
                'qualification' => $request->input('qualification'),
                'school_name' => $request->input('school_name'),
                'bio' => $request->input('bio'),
            ]);

            return $user;
        });

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => new AuthUserResource($user->load('teacher')),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }
}
