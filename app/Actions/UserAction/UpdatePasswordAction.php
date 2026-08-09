<?php

namespace App\Actions\UserAction;

use App\Models\User;

class UpdatePasswordAction
{
    public function handle(User $user, string $newPassword): User
    {
        $user->update([
            'password' => $newPassword,
        ]);

        return $user;
    }
}
