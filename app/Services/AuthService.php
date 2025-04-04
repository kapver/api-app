<?php

namespace App\Services;

use App\Exceptions\ExistingUserException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(
        private TokenService $tokenService,
    ) {
    }

    public function getRegistrationToken(array $context): string
    {
        return $this->tokenService->generate($context);
    }

    public function checkRegistrationToken(string $client_token, array $context): bool
    {
        return $this->tokenService->validate($client_token, $context);
    }

    public function register(array $data): User
    {
        $existingUser = User::query()
            ->where('email', $data['email'])
            ->orWhere('phone', $data['phone'])
            ->first();

        if ($existingUser) {
            throw new ExistingUserException();
        }

        $user = User::firstOrNew($data);
        $user->password = Hash::make('password');
        $user->save();

        $this->tokenService->invalidate();

        return $user;
    }


}