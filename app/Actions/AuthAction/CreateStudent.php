<?php

namespace App\Actions\AuthAction;

use App\Http\Requests\Auth\RegisterStudentRequest;
use App\Http\Resources\Auth\UserResource as AuthUserResource;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateStudent
{
    /**
     * Handle student creation and token generation.
     *
     * @return array{user: AuthUserResource, access_token: string, token_type: string}
     */
    public function handle(RegisterStudentRequest $request): array
    {
        $user = DB::transaction(function () use ($request) {
            $avatarPath = null;
            if ($request->hasFile('avatar_path')) {
                $avatarPath = $request->file('avatar_path')->store('avatars', 'public');
            } elseif (is_string($request->input('avatar_path'))) {
                $avatarPath = $request->input('avatar_path');
            }

            $user = User::create([
                'full_name'   => $request->input('full_name'),
                'email'       => $request->input('email'),
                'password'    => Hash::make($request->input('password')),
                'role'        => 'student',
                'avatar_path' => $avatarPath,
            ]);

            Student::create([
                'user_id'          => $user->id,
                'student_number'   => 'STU-' . strtoupper(Str::random(8)),
                'grade_level'      => $request->input('grade_level'),
                'class_section'    => $request->input('class_section'),
                'school_name'      => $request->input('school_name'),
                'birthday'         => $request->input('birthday'),
                'guardian_name'    => $request->input('guardian_name'),
                'guardian_contact' => $request->input('guardian_contact'),
            ]);

            return $user;
        });

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user'         => new AuthUserResource($user->load('student')),
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ];
    }
}
