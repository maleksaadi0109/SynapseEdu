<?php

namespace App\Http\Controllers;

use App\Actions\AuthAction\LoginAction;
use App\Http\Requests\Auth\LoginRequest;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        return (new LoginAction)->handle($request);
    }
}
