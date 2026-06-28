<?php

declare(strict_types=1);

namespace App\DTO\Product;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'Product', required: ['id', 'name', 'price', 'stock'])]
final readonly class ProductResponse
{
    public function __construct(
        #[OA\Property(type: 'integer', example: 1)]
        public int $id,
        #[OA\Property(type: 'string', example: 'Ban Motor IRC')]
        public string $name,
        #[OA\Property(type: 'string', nullable: true, example: 'Ban tubeless berkualitas tinggi')]
        public ?string $description,
        #[OA\Property(type: 'integer', example: 75000)]
        public int $price,
        #[OA\Property(type: 'integer', example: 20)]
        public int $stock,
        #[OA\Property(type: 'string', nullable: true, example: 'motor')]
        public ?string $category,
        #[OA\Property(type: 'string', nullable: true, example: 'http://localhost:8000/uploads/ban.jpg')]
        public ?string $image_url,
        #[OA\Property(type: 'string', example: '2026-06-28T10:00:00Z')]
        public string $created_at,
    ) {}
}
