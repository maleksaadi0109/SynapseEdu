<?php

namespace App\Http\Controllers\User;

use App\Actions\UserAction\GetProfileAction;
use App\Actions\UserAction\UpdateProfileAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Resources\Auth\UserResource;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request, GetProfileAction $action): UserResource
    {
        $user = $action->handle($request);

        return new UserResource($user);
    }

    public function update(UpdateProfileRequest $request, UpdateProfileAction $action): UserResource
    {
        $user = $action->handle($request->user(), $request->validated());

        return new UserResource($user);
    }
}
