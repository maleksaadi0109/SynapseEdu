<?php

namespace App\Actions\UserAction;

use App\Models\User;
use Illuminate\Http\Request;

class GetProfileAction
{
    public function handle(Request $request): User
    {
        return $request->user()->load(['student', 'teacher']);
    }
}
