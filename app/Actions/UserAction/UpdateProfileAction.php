<?php

namespace App\Actions\UserAction;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdateProfileAction
{
    public function __construct(
        private UpdateStudentAction $updateStudentAction,
        private UpdateTeacherAction $updateTeacherAction
    ) {}

    public function handle(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $userFields = array_intersect_key($data, array_flip([
                'full_name',
                'email',
                'avatar_path',
            ]));

            if (! empty($userFields)) {
                $user->update($userFields);
            }

            match ($user->role) {
                'student' => $user->student ? $this->updateStudentAction->handle($user->student, $data) : null,
                'teacher' => $user->teacher ? $this->updateTeacherAction->handle($user->teacher, $data) : null,
                default => null,
            };

            return $user->load($user->role);
        });
    }
}
