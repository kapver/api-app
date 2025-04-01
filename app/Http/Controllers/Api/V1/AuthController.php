<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ExistingUserException;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Services\AuthService;
use App\Services\PhotoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function token(Request $request, AuthService $authService): JsonResponse
    {
        $client_ip = $request->server('REMOTE_ADDR');
        $client_agent = $request->server('HTTP_USER_AGENT');

        return response()->json([
            'success' => true,
            'token' => $authService->getRegistrationToken(
                $client_ip,
                $client_agent,
            ),
        ]);
    }

    public function register(
        RegisterUserRequest $request,
        AuthService $authService,
        PhotoService $photoService,
    ): JsonResponse
    {
        try {
            $data = $request->validated();

            $user = $authService->register($data);

            // $photo = $data['photo'];
            // unset($data['photo']);
            // if ($photo) {
            //     $photo_path = $photoService->upload($photo);
            //     $user->updatePhotoPath($photo_path);
            // }

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
}