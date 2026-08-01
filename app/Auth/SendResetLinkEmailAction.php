<?php

namespace App\Auth;

use App\Http\Requests\ForgotPassowrdRequest;
use Illuminate\Support\Facades\Password;

class SendResetLinkEmailAction
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function handle(ForgotPassowrdRequest $request)
    {
        $request->validated();
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
                    ? response()->json(['message' => __($status)], 200)
                    : response()->json(['message' => __($status)], 422);
    }
}
