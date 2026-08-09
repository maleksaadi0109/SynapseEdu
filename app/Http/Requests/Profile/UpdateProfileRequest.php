<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->user();

        $rules = [
            'full_name' => ['sometimes', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'avatar_path' => ['nullable', 'image', 'max:2048'],
        ];

        return match ($user->role) {
            'student' => array_merge($rules, [
                'grade_level' => ['sometimes', 'string', 'max:255'],
                'class_section' => ['nullable', 'string', 'max:255'],
                'school_name' => ['nullable', 'string', 'max:255'],
                'birthday' => ['nullable', 'date'],
                'guardian_name' => ['nullable', 'string', 'max:255'],
                'guardian_contact' => ['nullable', 'string', 'max:255'],
            ]),
            'teacher' => array_merge($rules, [
                'department' => ['sometimes', 'string', 'max:255'],
                'specialization' => ['nullable', 'string', 'max:255'],
                'qualification' => ['nullable', 'string', 'max:255'],
                'school_name' => ['nullable', 'string', 'max:255'],
                'bio' => ['nullable', 'string'],
            ]),
            default => $rules,
        };
    }
}
