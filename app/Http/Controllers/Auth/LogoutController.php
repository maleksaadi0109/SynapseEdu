<?php

namespace App\Http\Controllers\Auth;

use App\Actions\AuthAction\LogoutAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function logout(Request $request, LogoutAction $logoutAction): JsonResponse
    {
        $logoutAction->handle($request);

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }
}
