<?php

namespace ShipSaasReducer\Tests\App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use ShipSaasReducer\Tests\App\Http\Resources\UserResource;
use ShipSaasReducer\Tests\App\Models\User;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $users = $request->has('pagination')
            ? User::paginate(2)
            : User::all();

        return UserResource::collection($users)->response();
    }

    public function show(string $userId): JsonResponse
    {
        $user = User::findOrFail($userId);
        return UserResource::make($user)->response();
    }
}
