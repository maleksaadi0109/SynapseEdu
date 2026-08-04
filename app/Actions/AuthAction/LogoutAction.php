<?php

namespace App\Actions\AuthAction;

use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class LogoutAction
{
    public function handle(Request $request): void
    {
        $token = $request->user()->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        } else {
            $request->user()->tokens()->delete();
        }
    }
}
