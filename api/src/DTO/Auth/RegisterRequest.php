<?php

declare(strict_types=1);

namespace App\DTO\Auth;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'RegisterRequest', required: ['email', 'password'])]
final readonly class RegisterRequest
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'email', example: 'user@example.com')]
        public string $email,
        #[OA\Property(type: 'string', minLength: 8, example: 'password123')]
        public string $password,
        #[OA\Property(type: 'string', enum: ['user', 'admin'], default: 'user', example: 'user')]
        public string $role = 'user',
    ) {}
}
