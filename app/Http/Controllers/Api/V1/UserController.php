<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function index(): UserCollection
    {
        $count = request()->input('count', 15);
        $users = User::query()
            ->paginate($count)
            ->withQueryString();

        return new UserCollection($users)->additional([
            'success' => true,
        ]);
    }

    public function show($id): JsonResponse
    {
        $user = User::find($id);

        if (empty($user)) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ]);
        }

        return response()->json([
            'success' => true,
            'user' => new UserResource($user),
        ]);
    }
}
