<?php

declare(strict_types=1);

namespace App\DTO\Order;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Order',
    required: ['id', 'user_id', 'status', 'total', 'created_at'],
)]
final readonly class OrderResponse
{
    public function __construct(
        #[OA\Property(type: 'integer', example: 1)]
        public int $id,

        #[OA\Property(type: 'integer', example: 3)]
        public int $user_id,

        #[OA\Property(
            type: 'string',
            enum: ['pending', 'processing', 'shipped', 'completed', 'cancelled'],
            example: 'pending',
        )]
        public string $status,

        #[OA\Property(type: 'integer', example: 300000)]
        public int $total,

        #[OA\Property(
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/OrderItem'),
        )]
        public array $items,

        #[OA\Property(type: 'string', example: '2026-06-28T10:00:00Z')]
        public string $created_at,
    ) {}
}
