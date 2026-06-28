<?php

declare(strict_types=1);

namespace App\DTO;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'HealthResponse', required: ['status'])]
final readonly class HealthResponse
{
    public function __construct(
        #[OA\Property(type: 'string', example: 'ok')]
        public string $status,
    ) {}

    /** @return array{status: string} */
    public function toArray(): array
    {
        return ['status' => $this->status];
    }
}
