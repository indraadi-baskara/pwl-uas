<?php

declare(strict_types=1);

namespace App\DTO\Cart;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Cart',
    required: ['id', 'items', 'total'],
)]
final readonly class CartResponse
{
    public function __construct(
        #[OA\Property(type: 'integer', example: 1)]
        public int $id,

        #[OA\Property(
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/CartItem'),
        )]
        public array $items,

        #[OA\Property(type: 'integer', example: 300000)]
        public int $total,
    ) {}
}
