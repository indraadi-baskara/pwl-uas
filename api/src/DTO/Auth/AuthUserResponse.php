<?php

declare(strict_types=1);

namespace App\DTO\Auth;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'AuthUserResponse', required: ['id', 'email', 'role'])]
final readonly class AuthUserResponse
{
    public function __construct(
        #[OA\Property(type: 'integer', example: 1)]
        public int $id,
        #[OA\Property(type: 'string', format: 'email', example: 'admin@example.com')]
        public string $email,
        #[OA\Property(type: 'string', enum: ['user', 'admin'], example: 'admin')]
        public string $role,
    ) {}
}
