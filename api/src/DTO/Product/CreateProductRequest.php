<?php

declare(strict_types=1);

namespace App\DTO\Product;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'CreateProductRequest', required: ['name', 'price'])]
final readonly class CreateProductRequest
{
    public function __construct(
        #[OA\Property(type: 'string', example: 'Ban Motor IRC')]
        public string $name,
        #[OA\Property(type: 'integer', example: 75000)]
        public int $price,
        #[OA\Property(type: 'string', nullable: true)]
        public ?string $description,
        #[OA\Property(type: 'integer', example: 0)]
        public int $stock,
        #[OA\Property(type: 'string', nullable: true, example: 'motor')]
        public ?string $category,
    ) {}
}
