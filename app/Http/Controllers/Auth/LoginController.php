<?php

namespace App\Http\Controllers\Auth;

use App\Actions\AuthAction\LoginAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        return (new LoginAction)->handle($request);
    }
}
