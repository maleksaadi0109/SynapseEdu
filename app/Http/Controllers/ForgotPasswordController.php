<?php

namespace App\Http\Controllers;

use App\Auth\ResetPasswordAction;
use App\Auth\SendResetLinkEmailAction;
use App\Http\Requests\ForgotPassowrdRequest;
use App\Http\Requests\ResetPasswordRequest;

class ForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(ForgotPassowrdRequest $request)
    {
        return (new SendResetLinkEmailAction)->handle($request);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        return (new ResetPasswordAction)->handle($request);
    }
}
