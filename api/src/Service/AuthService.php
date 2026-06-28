<?php

declare(strict_types=1);

namespace App\Service;

use App\Config\Env;
use App\Model\RefreshToken;
use App\Model\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

final class AuthService
{
    public function login(string $email, string $password): array
    {
        $user = User::findByEmail($email);

        if ($user === null || !$user->verifyPassword($password)) {
            throw new \RuntimeException('Invalid email or password');
        }

        return $this->issueTokens($user);
    }

    public function refresh(string $plainRefreshToken): array
    {
        $token = RefreshToken::findValid($plainRefreshToken);

        if ($token === null) {
            // Replay detection: token exists but was already revoked → possible theft
            $existing = RefreshToken::findByHash($plainRefreshToken);
            if ($existing !== null && $existing->revoked) {
                RefreshToken::revokeAllForUser($existing->userId);
            }
            throw new \RuntimeException('Invalid or expired refresh token');
        }

        $user = User::findById($token->userId);

        if ($user === null) {
            throw new \RuntimeException('User not found');
        }

        // Rotation: revoke old token before issuing new ones
        RefreshToken::revoke($plainRefreshToken);

        return $this->issueTokens($user);
    }

    public function logout(string $plainRefreshToken): void
    {
        RefreshToken::revoke($plainRefreshToken);
    }

    public function register(string $email, string $password, string $role = 'user'): User
    {
        if (User::findByEmail($email) !== null) {
            throw new \RuntimeException('Email already registered');
        }

        return User::create($email, $password, $role);
    }

    /** @return array{access_token: string, refresh_token: string, user: User} */
    private function issueTokens(User $user): array
    {
        $accessToken  = $this->generateAccessToken($user);
        $refreshToken = $this->generateRefreshToken($user);

        return [
            'access_token'  => $accessToken,
            'refresh_token' => $refreshToken,
            'user'          => $user,
        ];
    }

    private function generateAccessToken(User $user): string
    {
        $now = time();

        return JWT::encode([
            'sub'   => $user->id,
            'email' => $user->email,
            'role'  => $user->role,
            'iat'   => $now,
            'exp'   => $now + Env::int('JWT_ACCESS_TTL'),
        ], Env::get('JWT_SECRET'), 'HS256');
    }

    private function generateRefreshToken(User $user): string
    {
        $plain = bin2hex(random_bytes(64));
        RefreshToken::create($user->id, $plain, Env::int('JWT_REFRESH_TTL'));

        return $plain;
    }

    /** @return array{sub: int, email: string, role: string} */
    public function decodeAccessToken(string $token): array
    {
        $decoded = JWT::decode($token, new Key(Env::get('JWT_SECRET'), 'HS256'));

        return [
            'sub'   => (int) $decoded->sub,
            'email' => (string) $decoded->email,
            'role'  => (string) $decoded->role,
        ];
    }
}
