<?php

namespace App\Services;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class TokenService
{
    protected static string $validated_key;

    public function generate(array $context = []): string
    {
        $token = Crypt::encrypt($context);
        $token_key = self::getTokenKey($token, $context);

        if (!Cache::has($token_key)) {
            $lifetime = (int) config('app.registration_token_lifetime', 40);
            Cache::put($token_key, $token, now()->addMinutes($lifetime));
        }

        return Cache::get($token_key);
    }

    public function validate(string $token, array $context = []): bool
    {
        $token_key = self::getTokenKey($token, $context);
        $stored_token = Cache::get($token_key);

        if ($token && $stored_token && $token === $stored_token) {
            try {
                $contextHash = hash('sha256', join('', array_values($context)));
                $decryptHash = hash('sha256', join('', array_values(Crypt::decrypt($token))));
                if ($decryptHash === $contextHash) {
                    self::$validated_key = $token_key;
                    return true;
                }
            } catch (DecryptException) {
                return false;
            }
        }

        return false;
    }

    public function invalidate(): void
    {
        Cache::delete(self::$validated_key);
    }

    private static function getTokenKey(string $token, array $context): string
    {
        $base = empty($context)
            ? $token
            : join('', array_values($context));

        return md5($base);
    }
}