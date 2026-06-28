<?php

declare(strict_types=1);

namespace App\Model;

final readonly class OrderItem
{
    public function __construct(
        public int    $id,
        public int    $orderId,
        public ?int   $productId,
        public string $name,
        public int    $price,
        public int    $quantity,
    ) {}

    public function subtotal(): int
    {
        return $this->price * $this->quantity;
    }

    /** @param array<string, mixed> $row */
    public static function fromRow(array $row): self
    {
        return new self(
            id:        (int) $row['id'],
            orderId:   (int) $row['order_id'],
            productId: isset($row['product_id']) ? (int) $row['product_id'] : null,
            name:      (string) $row['name'],
            price:     (int) $row['price'],
            quantity:  (int) $row['quantity'],
        );
    }
}
