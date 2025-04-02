<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
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
}