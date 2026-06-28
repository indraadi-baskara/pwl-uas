<?php

declare(strict_types=1);

namespace App\Http;

use App\Service\AuthService;

final class Auth
{
    /** @return array{sub: int, email: string, role: string}|null */
    public static function user(): ?array
    {
        $token = $_COOKIE['access_token'] ?? '';

        if ($token === '') {
            return null;
        }

        try {
            return (new AuthService())->decodeAccessToken($token);
        } catch (\Exception) {
            return null;
        }
    }

    /** @return array{sub: int, email: string, role: string} */
    public static function requireAuth(): array
    {
        $user = self::user();

        if ($user === null) {
            Response::unauthorized();
        }

        return $user;
    }

    /** @return array{sub: int, email: string, role: string} */
    public static function requireAdmin(): array
    {
        $user = self::requireAuth();

        if ($user['role'] !== 'admin') {
            Response::forbidden();
        }

        return $user;
    }
}
