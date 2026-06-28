<?php

declare(strict_types=1);

namespace App\DTO\Order;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UpdateOrderStatusRequest',
    required: ['status'],
)]
final readonly class UpdateOrderStatusRequest
{
    public function __construct(
        #[OA\Property(
            type: 'string',
            enum: ['pending', 'processing', 'shipped', 'completed', 'cancelled'],
            example: 'processing',
        )]
        public string $status,
    ) {}
}
