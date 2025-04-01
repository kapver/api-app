<?php

namespace App\Services;

use App\Exceptions\ExistingUserException;
use App\Models\User;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function getRegistrationToken(string $client_ip, string $client_agent): string
    {
        $client_hash = hash('sha256', $client_ip.$client_agent);
        $token_key = 'token_' . $client_hash;

        if (!Cache::has($token_key)) {
            $token = Crypt::encrypt([
                'client_ip' => $client_ip,
                'client_agent' => $client_agent,
            ]);
            Cache::put($token_key, $token, now()->addMinutes(40));
        } else {
            $token = Cache::get($token_key);
        }

        return $token;
    }

    public function checkRegistrationToken(string $client_token, string $client_ip, string $client_agent): bool
    {
        $client_hash = hash('sha256', $client_ip.$client_agent);
        $token_key = 'token_' . $client_hash;
        $stored_token = Cache::get($token_key);

        if ($client_token && $stored_token === $client_token) {
            try {
                $decrypted = Crypt::decrypt($client_token);
                return $decrypted['client_ip'] === $client_ip && $decrypted['client_agent'] === $client_agent;
            } catch (DecryptException) {
                return false;
            }
        }

        return false;
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

        return $user;
    }
}