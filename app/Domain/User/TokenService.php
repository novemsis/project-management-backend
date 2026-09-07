<?php

namespace App\Domain\User;

use Laravel\Sanctum\NewAccessToken;

class TokenService
{
    public function createToken(User $user): NewAccessToken
    {
        $tokenExpiration = now()->addMinutes(240);
        return $user->createToken('auth_token', expiresAt: $tokenExpiration);
    }
}
