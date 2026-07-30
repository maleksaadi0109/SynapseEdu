<?php

namespace App\Http\Controllers;

use App\Actions\AuthAction\RegisterUserAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    /**
     * Handle user registration for Students and Teachers.
     */
    public function register(Request $request, RegisterUserAction $action): JsonResponse
    {
        try {
            $result = $action->handle($request);
            $role = strtolower((string) $request->input('role'));

            return response()->json([
                'message' => ucfirst($role).' registered successfully',
                'data' => $result['user'],
                'access_token' => $result['access_token'],
                'token_type' => $result['token_type'],
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
