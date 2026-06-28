<?php

declare(strict_types=1);

namespace App\DTO\Order;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'OrderItem',
    required: ['id', 'name', 'price', 'quantity', 'subtotal'],
)]
final readonly class OrderItemResponse
{
    public function __construct(
        #[OA\Property(type: 'integer', example: 1)]
        public int $id,

        #[OA\Property(type: 'integer', nullable: true, example: 5)]
        public ?int $product_id,

        #[OA\Property(type: 'string', example: 'Ban Motor IRC')]
        public string $name,

        #[OA\Property(type: 'integer', example: 75000)]
        public int $price,

        #[OA\Property(type: 'integer', example: 2)]
        public int $quantity,

        #[OA\Property(type: 'integer', example: 150000)]
        public int $subtotal,
    ) {}
}
