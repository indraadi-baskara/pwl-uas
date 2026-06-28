<?php

declare(strict_types=1);

namespace App\Model;

final readonly class CartItemRow
{
    public function __construct(
        public int     $id,
        public int     $productId,
        public string  $productName,
        public ?string $productImagePath,
        public int     $price,
        public int     $quantity,
    ) {}

    public function subtotal(): int
    {
        return $this->price * $this->quantity;
    }

    public function imageUrl(): ?string
    {
        if ($this->productImagePath === null) {
            return null;
        }

        $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';

        return "{$scheme}://{$host}/uploads/{$this->productImagePath}";
    }

    /** @param array<string, mixed> $row */
    public static function fromRow(array $row): self
    {
        return new self(
            id:               (int) $row['id'],
            productId:        (int) $row['product_id'],
            productName:      (string) $row['product_name'],
            productImagePath: isset($row['image_path']) ? (string) $row['image_path'] : null,
            price:            (int) $row['price'],
            quantity:         (int) $row['quantity'],
        );
    }
}
