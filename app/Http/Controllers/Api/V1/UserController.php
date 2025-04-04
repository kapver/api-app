<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ExistingUserException;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AuthService;
use App\Services\PhotoService;
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

    public function store(
        RegisterUserRequest $request,
        AuthService $authService,
        PhotoService $photoService
    ): JsonResponse
    {
        try {
            $data = $request->validated();
            $user = $authService->register($data);

            if ($data['photo']) {
                $user->photo = $photoService->upload($data['photo']);
                $user->save();
            }

            return response()->json([
                'success' => true,
                'user_id' => $user->id,
                'message' => 'New user successfully registered',
            ], 201);
        } catch (ExistingUserException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
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
