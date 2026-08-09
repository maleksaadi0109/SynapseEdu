<?php

namespace App\Http\Controllers;

use App\Actions\UserAction\UpdatePasswordAction;
use App\Http\Requests\Profile\UpdatePasswordRequest;
use Illuminate\Http\JsonResponse;

class UpdatePasswordController extends Controller
{
    public function update(UpdatePasswordRequest $request, UpdatePasswordAction $action): JsonResponse
    {
        $action->handle($request->user(), $request->input('password'));

        return response()->json([
            'message' => 'Password updated successfully',
        ]);
    }
}
