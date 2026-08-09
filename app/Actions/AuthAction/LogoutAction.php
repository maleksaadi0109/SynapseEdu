<?php

namespace App\Actions\AuthAction;

use Illuminate\Http\Request;

class LogoutAction
{
    public function handle(Request $request): void
    {
        $request->user()->tokens()->delete();
    }
}
