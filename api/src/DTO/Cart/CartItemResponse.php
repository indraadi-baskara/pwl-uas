<?php

declare(strict_types=1);

namespace App\DTO\Cart;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CartItem',
    required: ['id', 'product_id', 'product_name', 'price', 'quantity', 'subtotal'],
)]
final readonly class CartItemResponse
{
    public function __construct(
        #[OA\Property(type: 'integer', example: 1)]
        public int $id,

        #[OA\Property(type: 'integer', example: 5)]
        public int $product_id,

        #[OA\Property(type: 'string', example: 'Ban Motor IRC')]
        public string $product_name,

        #[OA\Property(type: 'string', nullable: true, example: 'http://localhost:8000/uploads/ban.jpg')]
        public ?string $product_image_url,

        #[OA\Property(type: 'integer', example: 75000)]
        public int $price,

        #[OA\Property(type: 'integer', example: 2)]
        public int $quantity,

        #[OA\Property(type: 'integer', example: 150000)]
        public int $subtotal,
    ) {}
}
