<?php

declare(strict_types=1);

namespace App\Model;

use App\Config\Database;
use PDO;

final readonly class Cart
{
    /**
     * @param list<CartItemRow> $items
     */
    public function __construct(
        public int    $id,
        public int    $userId,
        public array  $items,
        public string $updatedAt,
    ) {}

    public function total(): int
    {
        return array_sum(array_map(
            static fn (CartItemRow $item) => $item->subtotal(),
            $this->items,
        ));
    }

    public static function findOrCreateByUserId(int $userId): self
    {
        $pdo  = Database::connection();
        $stmt = $pdo->prepare(
            'INSERT INTO carts (user_id)
             VALUES (:uid)
             ON CONFLICT (user_id)
             DO UPDATE SET updated_at = NOW()
             RETURNING id, user_id, updated_at'
        );
        $stmt->execute(['uid' => $userId]);

        /** @var array{id: int|string, user_id: int|string, updated_at: string} $row */
        $row   = $stmt->fetch(PDO::FETCH_ASSOC);
        $cartId = (int) $row['id'];
        $items  = self::loadItems($cartId);

        return new self(
            id:        $cartId,
            userId:    (int) $row['user_id'],
            items:     $items,
            updatedAt: (string) $row['updated_at'],
        );
    }

    public static function getWithItems(int $userId): self
    {
        return self::findOrCreateByUserId($userId);
    }

    /**
     * @return list<CartItemRow>
     */
    public static function loadItems(int $cartId): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT ci.id,
                    ci.product_id,
                    p.name        AS product_name,
                    p.price,
                    ci.quantity,
                    p.image_path
               FROM cart_items ci
               JOIN products   p ON p.id = ci.product_id
              WHERE ci.cart_id = :cid
              ORDER BY ci.created_at ASC'
        );
        $stmt->execute(['cid' => $cartId]);

        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(static fn (array $row) => CartItemRow::fromRow($row), $rows);
    }

    public static function addItem(int $userId, int $productId, int $quantity): self
    {
        $cart = self::findOrCreateByUserId($userId);

        $stmt = Database::connection()->prepare(
            'INSERT INTO cart_items (cart_id, product_id, quantity)
             VALUES (:cart_id, :product_id, :qty)
             ON CONFLICT (cart_id, product_id)
             DO UPDATE SET quantity   = cart_items.quantity + :qty,
                           updated_at = NOW()'
        );
        $stmt->execute([
            'cart_id'    => $cart->id,
            'product_id' => $productId,
            'qty'        => $quantity,
        ]);

        return self::getWithItems($userId);
    }

    public static function setItemQuantity(int $userId, int $itemId, int $quantity): self
    {
        // Verify the item belongs to this user's cart before updating.
        $check = Database::connection()->prepare(
            'UPDATE cart_items ci
                SET quantity   = :qty,
                    updated_at = NOW()
               FROM carts c
              WHERE ci.id      = :item_id
                AND ci.cart_id = c.id
                AND c.user_id  = :user_id'
        );
        $check->execute([
            'qty'     => $quantity,
            'item_id' => $itemId,
            'user_id' => $userId,
        ]);

        if ($check->rowCount() === 0) {
            throw new \RuntimeException('Cart item not found');
        }

        return self::getWithItems($userId);
    }

    public static function removeItem(int $userId, int $itemId): self
    {
        $stmt = Database::connection()->prepare(
            'DELETE FROM cart_items ci
                   USING carts c
                   WHERE ci.id      = :item_id
                     AND ci.cart_id = c.id
                     AND c.user_id  = :user_id'
        );
        $stmt->execute([
            'item_id' => $itemId,
            'user_id' => $userId,
        ]);

        if ($stmt->rowCount() === 0) {
            throw new \RuntimeException('Cart item not found');
        }

        return self::getWithItems($userId);
    }

    public static function clearByUserId(int $userId): void
    {
        $stmt = Database::connection()->prepare(
            'DELETE FROM cart_items ci
                   USING carts c
                   WHERE ci.cart_id = c.id
                     AND c.user_id  = :user_id'
        );
        $stmt->execute(['user_id' => $userId]);
    }

    public static function findItemForUser(int $itemId, int $userId): ?CartItemRow
    {
        $stmt = Database::connection()->prepare(
            'SELECT ci.id,
                    ci.product_id,
                    p.name       AS product_name,
                    p.price,
                    ci.quantity,
                    p.image_path
               FROM cart_items ci
               JOIN products   p ON p.id = ci.product_id
               JOIN carts      c ON c.id = ci.cart_id
              WHERE ci.id     = :item_id
                AND c.user_id = :user_id
              LIMIT 1'
        );
        $stmt->execute([
            'item_id' => $itemId,
            'user_id' => $userId,
        ]);

        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row !== false ? CartItemRow::fromRow($row) : null;
    }
}
