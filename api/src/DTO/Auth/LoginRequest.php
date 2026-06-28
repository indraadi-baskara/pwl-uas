<?php

declare(strict_types=1);

namespace App\DTO\Auth;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'LoginRequest', required: ['email', 'password'])]
final readonly class LoginRequest
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'email', example: 'admin@example.com')]
        public string $email,
        #[OA\Property(type: 'string', minLength: 8, example: 'password123')]
        public string $password,
    ) {}
}
