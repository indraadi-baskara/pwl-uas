<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Request;
use App\Http\Response;
use App\Model\User;
use App\Service\AuthService;
use App\Service\RateLimiter;
use App\Validation\Validator;
use OpenApi\Attributes as OA;

final class AuthController
{
    private AuthService $auth;

    public function __construct()
    {
        $this->auth = new AuthService();
    }

    #[OA\Post(
        path: '/auth/login',
        operationId: 'authLogin',
        summary: 'Login and receive token cookies',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/LoginRequest'),
        ),
        responses: [
            new OA\Response(response: 200, description: 'Authenticated', content: new OA\JsonContent(ref: '#/components/schemas/AuthUserResponse')),
            new OA\Response(response: 400, description: 'Validation error'),
            new OA\Response(response: 401, description: 'Invalid credentials'),
            new OA\Response(response: 429, description: 'Rate limit exceeded'),
        ],
    )]
    public function login(Request $request): never
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        if (!RateLimiter::check("login:{$ip}")) {
            Response::error('Too many attempts. Try again later.', 429);
        }

        $v = (new Validator($request->body))
            ->required('email')->email('email')
            ->required('password');

        if (!$v->passes()) {
            Response::error($v->firstError(), 400);
        }

        try {
            $result = $this->auth->login(
                (string) $request->body['email'],
                (string) $request->body['password'],
            );
        } catch (\RuntimeException $e) {
            Response::unauthorized($e->getMessage());
        }

        $this->setTokenCookies($result['access_token'], $result['refresh_token']);

        Response::success($this->userPayload($result['user']));
    }

    #[OA\Post(
        path: '/auth/register',
        operationId: 'authRegister',
        summary: 'Register a new user',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/RegisterRequest'),
        ),
        responses: [
            new OA\Response(response: 201, description: 'User created', content: new OA\JsonContent(ref: '#/components/schemas/AuthUserResponse')),
            new OA\Response(response: 400, description: 'Validation error'),
            new OA\Response(response: 429, description: 'Rate limit exceeded'),
        ],
    )]
    public function register(Request $request): never
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        if (!RateLimiter::check("register:{$ip}")) {
            Response::error('Too many attempts. Try again later.', 429);
        }

        $v = (new Validator($request->body))
            ->required('email')->email('email')
            ->required('password')->minLength('password', 8);

        if (!$v->passes()) {
            Response::error($v->firstError(), 400);
        }

        try {
            $user = $this->auth->register(
                (string) $request->body['email'],
                (string) $request->body['password'],
            );
        } catch (\RuntimeException $e) {
            Response::error($e->getMessage(), 409);
        }

        Response::success($this->userPayload($user), 201);
    }

    #[OA\Post(
        path: '/auth/refresh',
        operationId: 'authRefresh',
        summary: 'Rotate tokens using refresh token cookie',
        responses: [
            new OA\Response(response: 200, description: 'Tokens rotated'),
            new OA\Response(response: 401, description: 'Invalid or expired refresh token'),
            new OA\Response(response: 429, description: 'Rate limit exceeded'),
        ],
    )]
    public function refresh(Request $request): never
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        if (!RateLimiter::check("refresh:{$ip}")) {
            Response::error('Too many attempts. Try again later.', 429);
        }

        $plain = $_COOKIE['refresh_token'] ?? '';

        if ($plain === '') {
            Response::unauthorized('No refresh token provided');
        }

        try {
            $result = $this->auth->refresh($plain);
        } catch (\RuntimeException $e) {
            // Possible replay: revocation already happened inside refresh()
            Response::unauthorized($e->getMessage());
        }

        $this->setTokenCookies($result['access_token'], $result['refresh_token']);

        Response::success($this->userPayload($result['user']));
    }

    #[OA\Post(
        path: '/auth/logout',
        operationId: 'authLogout',
        summary: 'Logout and clear token cookies',
        responses: [
            new OA\Response(response: 200, description: 'Logged out'),
        ],
    )]
    #[OA\Get(
        path: '/auth/me',
        operationId: 'authMe',
        summary: 'Return the authenticated user from access token cookie',
        responses: [
            new OA\Response(response: 200, description: 'Authenticated user', content: new OA\JsonContent(ref: '#/components/schemas/AuthUserResponse')),
            new OA\Response(response: 401, description: 'Not authenticated'),
        ],
    )]
    public function me(Request $request): never
    {
        $token = $_COOKIE['access_token'] ?? '';

        if ($token === '') {
            Response::unauthorized();
        }

        try {
            $payload = $this->auth->decodeAccessToken($token);
        } catch (\Exception) {
            Response::unauthorized('Invalid or expired token');
        }

        Response::success([
            'id'    => $payload['sub'],
            'email' => $payload['email'],
            'role'  => $payload['role'],
        ]);
    }

    public function logout(Request $request): never
    {
        $plain = $_COOKIE['refresh_token'] ?? '';

        if ($plain !== '') {
            $this->auth->logout($plain);
        }

        $this->clearTokenCookies();

        Response::success(['message' => 'Logged out']);
    }

    private function setTokenCookies(string $accessToken, string $refreshToken): void
    {
        $secure   = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        $sameSite = 'Strict';

        setcookie('access_token', $accessToken, [
            'expires'  => time() + (int) ($_ENV['JWT_ACCESS_TTL'] ?? 900),
            'path'     => '/',
            'httponly' => true,
            'secure'   => $secure,
            'samesite' => $sameSite,
        ]);

        setcookie('refresh_token', $refreshToken, [
            'expires'  => time() + (int) ($_ENV['JWT_REFRESH_TTL'] ?? 604800),
            'path'     => '/auth/refresh',
            'httponly' => true,
            'secure'   => $secure,
            'samesite' => $sameSite,
        ]);
    }

    private function clearTokenCookies(): void
    {
        $secure   = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        $sameSite = 'Strict';

        setcookie('access_token', '', [
            'expires'  => 1,
            'path'     => '/',
            'httponly' => true,
            'secure'   => $secure,
            'samesite' => $sameSite,
        ]);

        setcookie('refresh_token', '', [
            'expires'  => 1,
            'path'     => '/auth/refresh',
            'httponly' => true,
            'secure'   => $secure,
            'samesite' => $sameSite,
        ]);
    }

    /** @return array{id: int, email: string, role: string} */
    private function userPayload(User $user): array
    {
        return ['id' => $user->id, 'email' => $user->email, 'role' => $user->role];
    }
}
